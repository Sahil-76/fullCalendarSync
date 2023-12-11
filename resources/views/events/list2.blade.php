<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  
    <meta name="token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.5/main.min.css"
        integrity="sha512-1P/SRFqI1do4eNtBsGIAqIZIlnmOQkaY7ESI2vkl+q+hl9HSXmdPqotN0McmeZVyR4AWV+NvkP6pKOiVdY/V5A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css"
        integrity="sha512-bYPO5jmStZ9WI2602V2zaivdAnbAhtfzmxnEGh9RwtlI00I9s8ulGe4oBa5XxiC6tCITJH/QG70jswBhbLkxPw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Document</title>
</head>

<body>
    <h1>CALENDAR</h1>
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
    <!-- Button trigger modal -->
    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
        event
    </button> --}}

    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="eventModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" id="eventId">
                        <label for="title">Title</label>
                        <input type="text" placeholder="Enter Title" class="form-control" id="title" name="title" value="" required>
                    </div>
                    <div>
                        <label for="is_all_day">
                            All Day
                        </label>
                        <input type="checkbox" id="is_all_day" checked name="is_all_day" value="" required>
                    </div>
                    <div>
                        <label for="startDateTime"> Start Date/time</label>
                        <input type="text" placeholder="select start date" readonly class="form-control" id="startDateTime" name="startDate" value="" required>
                    </div>
                    <div>
                        <label for="endDateTime"> End Date/time</label>
                        <input type="text" placeholder="select end date" readonly class="form-control" id="endDateTime" name="endDate" value="" required>
                    </div>
                    <div>
                        <label for="description">Description</label>
                        <textarea placeholder="description" class="form-control" id="description" name="description"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary"onclick="SubmitEventFormData()">Save changes</button> --}}
                    {{-- <button onclick="myFunction()"id="subBtn">Submit</button> --}}
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" id="submitEvent">Submit</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js" referrerpolicy="no-referrer"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type>
        var calendar=null;
        document.addEventListener('DOMContentLoaded', function() {
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: new Date(),
                headerToolbar: {
                    left: 'prev,next,today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                url: '{{ route("refetch-events") }}', // Use single quotes here
                method: 'GET',
                failure: function() {
                    alert('There was an error fetching events!');
                },
            },
                dateClick: function(info) {
                //     console.log(info);
                //     initializeStartDateEndDateFormat('Y-m-d', true);
                //     $('#eventModal').modal('show');

                let startDate,endDate,allDay;
                // allDay=$('$is_all_day').prop('checked');
                allDay = $('#is_all_day').prop('checked');

                if(allDay)
                {
                    startDate=moment(info.date).format('YYYY-MM-DD');
                    endDate=moment(info.date).format('YYYY-MM-DD');
                    initializeStartDateEndDateFormat('Y-m-d',true);
                }else{
                    initializeStartDateEndDateFormat('Y-m-d H:i', false);
                    startDate=moment(info.date).format('YYYY-MM-DD');
                    endDate=moment(info.date).format('YYYY-MM-DD');
                }
                    $('#startDateTime').val(startDate);
                    $('#endDateTime').val(endDate);
                    //modalReset();
                    $('#eventModal').modal('show');


                 },
                //  eventClick:function(info)
                //  {
                //     console.log(info)
                //    // modalReset();
                //     $('#eventModal').modal('show');
                //     const event=info.event
                //     $('#title').val(event.title);
                //     $('#eventId').val(info.event.id);
                //     $('#description').val(event.extendProps.description);
                //     $('#startDateTime').val(event.extendProps.startDay);
                //     $('#endDateTime').val(event.extendProps.endDay);
                //     $('#is_all_day').prop('checked',event.allDay);
                //     $('#eventModal').modal('show');
                //      console.log(info)
                //      if(info.event.allDay)
                //      {
                //         initializeStartDateEndDateFormat('Y-m-d H:i', true);
                //      }else{
                //         initializeStartDateEndDateFormat('Y-m-d H:i', false);
                //      }
            

            });
    
            calendar.render();
    
            $('#is_all_day').change(function() {
                let is_all_day = $(this).prop('checked');
                if (is_all_day) {
                    let start = $('#startDateTime').val().slice(0, 10);
                    $('#startDateTime').val(start);
    
                    let endDateTime = $('#endDateTime').val().slice(0, 10);
                    $('#endDateTime').val(endDateTime);
                    initializeStartDateEndDateFormat('Y-m-d', is_all_day);
                } else {
                    let start = $('#startDateTime').val().slice(0, 10);
                    $('#startDateTime').val(start + '00:00');
    
                    let endDateTime = $('#endDateTime').val().slice(0, 10);
                    $('#endDateTime').val(endDateTime + '00:30');
                    initializeStartDateEndDateFormat('Y-m-d H:i', is_all_day);
                }
            });
    
        });
    
        function initializeStartDateEndDateFormat(format, allDay) {
            let timePicker = !allDay;
    
            // Initialize Bootstrap Datepicker
            $('#startDateTime').datepicker({
                format: format,
                autoclose: true,
            });
    
            $('#endDateTime').datepicker({
                format: format,
                autoclose: true,
            });
        }

$('#submitEvent').click(function () {
                // Handle form submission logic here
    console.log('Submit button clicked');
    let eventId = $('#eventId').val();
    let url = "{{ route('events.store') }}";
    let postData = {
        start: $('#startDateTime').val(),
        end: $('#endDateTime').val(),
        title: $('#title').val(),
        description: $('#description').val(),
        is_all_day: $('#is_all_day').prop('checked') ? 1 : 0
    };

    if (postData.is_all_day) {
        // postData.end = moment().add(1, "days").format('YYYY-MM-DD');
    }

    let postDataMethod = 'POST';

    // if (eventId) {
    //     url = '{{ url('/') }}' + '/events/' + $(eventId);
    //     postDataMethod = 'PUT';
    // }

    if (eventId) {
        // Corrected URL construction for update
        url = '{{ url('/') }}' + '/events/' + eventId;  // <-- Corrected line
        postDataMethod = 'PUT';
    }

    $.ajax({
        type: postDataMethod,
        url: url,
        dataType: "json",
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        data: postData,
        success: function (res) {
            if (res.success) {
                calendar.refetchEvents();
                $('#eventModal').modal('hide');
            } else {
                alert("Something went wrong");
            }
        },
        error: function (err) {
            // console.error(err);
            alert("An error occurred while processing the request.");
        }
    });
            });

    </script>


</body>

</html>
