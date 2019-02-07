<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Date difference Calculator</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <style type="text/css">
        .pb-4, .py-4 {
            padding-bottom: 10rem !important;
        }
        #form-response {
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Date difference Calculator</a>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Calculate difference between two dates</div>
                            <div class="card-body">
                                <input id="csrf-token" type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group row">
                                    <label for="start-date" class="col-md-4 col-form-label text-md-right">Start date</label>
                                    <div class="col-md-6">
                                        <input id="start-date" type="text" class="date-input form-control" name="start_date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="end-date" class="col-md-4 col-form-label text-md-right">End date</label>
                                    <div class="col-md-6">
                                        <input id="end-date" type="end_date" class="date-input form-control" name="end_date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked="checked" name="end_date_included" value="true" id="end-date-included">
                                            <label class="form-check-label" for="end-date-included">Include End date ?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <a href="#" id="calculate" class="btn btn-primary">Calculate</a>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <div class="response"><h4 id="form-response">Select dates and hit Enter</h4></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        // date-picker for start date and end date
        $('#start-date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
        }).on('changeDate', function() {
            if ($('#start-date').val() > $('#end-date').val()) {
                $('#end-date').val($('#start-date').val());
            }
            $('#end-date').datepicker('setStartDate', $('#start-date').val());
        });
        $('#end-date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $(document).ready(function() {

            // checkbox value
            endDateIncluded = $('#end-date-included').val();
            $('#end-date-included').on('change', function() {
                endDateIncluded = this.checked ? this.value : '';
            });

            // on submit
            $(document).on('click', '#calculate', function(event) {
                event.preventDefault();

                startDate = $('#start-date').val();
                endDate = $('#end-date').val();
                csrf_token = $('#csrf-token').val();
                if (startDate == '') {
                    iziToast.error({
                        maxWidth: 400,
                        timeout: 10000,
                        position: 'topRight',
                        title: 'Error Alert',
                        message: 'Please input start date.',
                    });
                } else if (endDate == '') {
                    iziToast.error({
                        maxWidth: 400,
                        timeout: 10000,
                        position: 'topRight',
                        title: 'Error Alert',
                        message: 'Please input end date.',
                    });
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('calculate') }}',
                        context: $(this),
                        data: {
                            'start_date': startDate,
                            'end_date': endDate,
                            'end_date_included': endDateIncluded,
                            '_token': csrf_token
                        },
                        beforeSend: function() {
                            $(this).attr('disabled', 'disabled');
                            $(this).html('Please Wait...');
                        },
                        complete: function() {
                            $(this).attr('disabled', false);
                            $(this).html('Calculate');
                        },
                        success: function(data) {
                            iziToast.success({
                                maxWidth: 400,
                                timeout: 10000,
                                position: 'topRight',
                                title: 'Success Alert',
                                message: data.message,
                            });
                            $('#form-response').text(data.message);
                        },
                        error: function(error) {
                            iziToast.error({
                                maxWidth: 400,
                                timeout: 10000,
                                position: 'topRight',
                                title: 'Error Alert',
                                message: 'Something went wrong, Please try after some time.',
                            });
                        }
                    });         
                }
            });
        });
    </script>
</body>
</html>