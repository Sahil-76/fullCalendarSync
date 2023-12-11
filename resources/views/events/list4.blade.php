@extends('layouts.app')

@push('css')
<meta name="token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css"
        integrity="sha512-bYPO5jmStZ9WI2602V2zaivdAnbAhtfzmxnEGh9RwtlI00I9s8ulGe4oBa5XxiC6tCITJH/QG70jswBhbLkxPw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #calendar a {
            color: #000000;
            text-decoration: none;
        }

        .mr-auto {
            margin-right: auto;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" id="eventId">
                        <label for="eventTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="eventTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day">
                            <label class="form-check-label" for="is_all_day">All Day Event</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventStart" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" id="eventStart" name="start" required>
                    </div>

                    <div class="mb-3">
                        <label for="eventEnd" class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control" id="eventEnd" name="end" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description"></textarea>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitEventFormData()">Save Event</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.5/main.min.js" referrerpolicy="no-referrer">
    </script>

    <script>
        var calender = null;
        jQuery(document).ready(function($) {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded.');
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                },
            });

            var calendarElement = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',
                initialDate: new Date(),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events : '{{route('refetch-events')}}',
                dateClick: function(info) {
                    let startDate, endDate, allDay;
                    allDay = $('#is_all_day').prop('checked');
                    if (allDay) {
                        startDate = moment(info.date).format("YYYY-MM-DD");
                        endDate = moment(info.date).format("YYYY-MM-DD");
                        initializeStartDateEndDateFormat('Y-m-d', true);
                    } else {
                        initializeStartDateEndDateFormat('Y-m-d H:i', false);
                        startDate = moment(info.date).format("YYYY-MM-DD HH:mm:ss");
                        endDate = moment(info.date).add(30, "minutes").format("YYYY-MM-DD HH:mm:ss");
                    }
                    $('#eventStart').val(startDate);
                    $('#eventEnd').val(endDate);
                    modalReset();
                    $('#eventModal').modal('show');
                }
            });

            $('#is_all_day').change(function() {
                let is_all_day = $(this).prop('checked');
                if (is_all_day) {
                    let start = $('#eventStart').val().slice(0, 10);
                    $('#eventStart').val(start);
                    let endDateTime = $('#eventEnd').val().slice(0, 10);
                    $('#eventEnd').val(endDateTime);
                    initializeStartDateEndDateFormat('Y-m-d', is_all_day);
                } else {
                    let start = $('#eventStart').val().slice(0, 10);
                    $('#eventStart').val(start + "T00:00");
                    let endDateTime = $('#eventEnd').val().slice(0, 10);
                    $('#eventEnd').val(endDateTime + "T00:30");
                    initializeStartDateEndDateFormat('Y-m-dTH:i', is_all_day);
                }
            });

            function initializeStartDateEndDateFormat(format, allDay) {
                let timePicker = !allDay;
                $('#eventStart').datetimepicker({
                    format: format,
                    timepicker: timePicker
                });
                $('#eventEnd').datetimepicker({
                    format: format,
                    timepicker: timePicker
                });
            }

            function modalReset() {
                $('#eventTitle').val("");
                $('#eventDescription').val("");
                $('#eventId').val("");
                $('#deleteEventBtn').hide();
            }

            calendar.render();
        });

        function submitEventFormData() {
            let eventId = $('#eventId').val();
            let url = "{{ route('events.store') }}";
            let isAllDay = $('#is_all_day').prop('checked'); 

            let postData = {
                start: $('#eventStart').val(),
                end: $('#eventEnd').val(),
                title: $('#eventTitle').val(),
                description: $('#eventDescription').val(),
                is_all_day: isAllDay ? 1 : 0, 
            };
            if (postData.is_all_day) {
                postData.end = moment().add(1, "days").format("YYYY-MM-DD");
            }
            if (eventId) {
                url = "{{ url('/') }}" + "events/" + eventId;
                postData.method = "PUT";
            }
            $.ajax({
                type: 'POST',
                url: url,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: postData,
                success: function(res) {
                    if (res.success) {
                        calendar.refetchEvents();
                        $('#eventModal').modal('hide');
                    } else {
                        alert('Something Went Wrong!!!');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error: ' + status);
                }
            });
        }
    </script>
@endpush