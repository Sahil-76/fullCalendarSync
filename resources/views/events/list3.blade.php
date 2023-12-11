<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.5/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css"
        integrity="sha512-bYPO5jmStZ9WI2602V2zaivdAnbAhtfzmxnEGh9RwtlI00I9s8ulGe4oBa5XxiC6tCITJH/QG70jswBhbLkxPw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <center>
        <h1>fullCalendar</h1>
    </center>
    <hr>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="eventModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModelLabel">Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" id="eventId">
                        <label for="title">Title</label>
                        <input type="text" placeholder="Enter Title" class="form-control" id="title"
                            name="title" value="" required>
                    </div>
                    <div>
                        <label for="is_all_day">All Day</label>
                        <input type="checkbox" id="is_all_day" checked name="is_all_day" value="" required>
                    </div>
                    <div>
                        <label for="startDateTimePicker">Start Date/time</label>
                        <input type="text" placeholder="Select date" readonly class="form-control"
                            id="startDateTimePicker" name="startDateTime" value="" required>
                    </div>
                    
                    <div>
                        <label for="endDateTimePicker">End Date/time</label>
                        <input type="text" placeholder="Select date" readonly class="form-control"
                            id="endDateTimePicker" name="endDateTime" value="" required>
                    </div>
                    
                    <div>
                        <label for="description">Description</label>
                        <textarea placeholder="Enter Description" class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submit"> Save Changes </button>
                        <button type="button" class="btn btn-danger mr-auto" style="display:none" id="deleteEventBtn" onclick="deleteEvent()">Delete Event</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.5/main.min.js" referrerpolicy="no-referrer"></script>
    <script type="module"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
        integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: new Date(),
                    headerToolbar: {
                        left: 'prev,next,today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    dateClick: function(info) {
                        let startDate, endDate, allDay;
                        allDay = $('#is_all_day').prop('checked');
        
                        if (allDay) {
                            startDate = moment(info.date).format('YYYY-MM-DD');
                            endDate = moment(info.date).format('YYYY-MM-DD');
                            initializeStartDateEndDateFormat('Y-m-d', true);
                        } else {
                            initializeStartDateEndDateFormat('Y-m-d H:i', false);
                            startDate = moment(info.date).format('YYYY-MM-DD HH:mm');
                            endDate = moment(info.date).add(30, 'minutes').format('YYYY-MM-DD HH:mm');
                        }
        
                        $('#startDateTime').val(startDate);
                        $('#endDateTime').val(endDate);
                        modalReset();
                        $('#eventModel').modal('show');
                    }
                });
        
                calendar.render();
        
                $('#is_all_day').change(function() {
                    let is_all_day = $(this).prop('checked');
                    let startValue = $('#startDateTimePicker').val();
                    let endValue = $('#endDateTimePicker').val();
        
                    if (is_all_day) {
                        let start = startValue ? startValue.slice(0, 10) : '';
                        $('#startDateTimePicker').val(start);
                        let end = endValue ? endValue.slice(0, 10) : '';
                        $('#endDateTimePicker').val(end);
                        initializeStartDateEndDateFormat('Y-m-d', is_all_day);
                    } else {
                        let start = startValue ? startValue.slice(0, 10) + '00:00' : '';
                        $('#startDateTimePicker').val(start);
                        let end = endValue ? endValue.slice(0, 10) + '00:30' : '';
                        $('#endDateTimePicker').val(end);
                        initializeStartDateEndDateFormat('Y-m-d H:i', is_all_day);
                    }
                });
        
                function initializeStartDateEndDateFormat(format, allDays) {
                    let timePicker = !allDays;
                    $('#startDateTimePicker').datetimepicker({
                        format: format,
                        timepicker: timePicker
                    });
                    $('#endDateTimePicker').datetimepicker({
                        format: format,
                        timepicker: timePicker
                    });
                }
        
                function modalReset() {
                    $("#title").val("");
                    $("#description").val("");
                    $('#deleteEventBtn').hide();
                }
        
                // function submitEventFormData() {
                    $('#submit').click(function () {
       
                    // console.log('submitEventFormData function called');
                    let eventId = $('#eventId').val();
                    let url = "{{ route('events.store') }}";
                    let postData = {
                        start: $('#startDateTime').val(),
                        end: $('#endDateTime').val(),
                        title: $('#title').val(),
                        description: $('#description').val(),
                        is_all_day: $('#is_all_day').prop('checked') ? 1 : 0
                    };
        
                    if (eventId) {
                        url = '{{ url('/') }}' + '/events/' + eventId;
                        postData._method = 'PUT';
                    }
                    // let csrfToken =$('meta[name="csrfToken"]').
        
                    $.ajax({
                        type: 'POST',
                        url: url,
                        dataType: 'json',
                        data: postData,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                        // success: function (res) {
                        //     if (res.success) {
                        //         calendar.refetchEvents();
                        //         $('#eventModal').modal('hide');
                        //     } else {
                        //         alert("something wrong");
                        //     }
                        // }
                    });
                })
            });
    </script>        
</body>
</html>
