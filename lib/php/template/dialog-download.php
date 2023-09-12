<?php

// 2023-07-23T09:24

$start_time = date( 'Y-m-d\T00:00' );
$end_time = date( 'Y-m-d\T23:59' );

?>

<div class="ws-forms-download-dialog">

    <h1>Download Responses</h1>

    <form id="ws-forms-download-responses">

        <p><strong>Time frame of responses</strong></p>

        <div>
            <label for="all-responses">
                <input type="radio" id="all-responses" name="responses_time_frame" value="all" checked />
                All responses
            </label>

            <label for="date-time-range">
                <input type="radio" id="date-time-range" class="wsf-reveal-hide" rel="ws-forms-date-time-ranges" name="responses_time_frame" value="date-time-range" />
                Date/time range
            </label>
        </div>
        
        <div id="ws-forms-date-time-ranges" class="ws-forms-hide">
            <div>
                <label for="date-time-range-start">
                    Start
                    <input type="datetime-local" id="date-time-range-start" name="responses_date_time_begin" value="<?php echo $start_time ?>" />
                </label>
            </div>
            <div>
                <label for="date-time-range-end">
                    End
                    <input type="datetime-local" id="date-time-range-end" name="responses_date_time_end" value="<?php echo $end_time ?>" />
                </label>
            </div>
        </div>

        <div style="margin-top:1rem">
            <span class="ws-form-dialog-button save-this button button-primary">Submit</span>
            <span class="ws-form-dialog-button cancel-this button">Cancel</span>
        </div>

    </form>

</div>