if (typeof submit_poll_url === "undefined") { var submit_poll_url = ""; }

$(function() {
    "use strict";

    $(document).ready(function() {
        get_polls();
        initVote();
    });

    // /* Auto refresh every 4 seconds */
    // var intervalId = window.setInterval(function(){
    //     get_polls();
    // }, 4000);

    /* Get Polls function */
    function get_polls() {
        $.ajax({
            type: "post",
            url: poll_url,
            success: function(data) {
                if (typeof data !== "undefined") {
                    if (typeof data.status !== "undefined" && data.status == true) {
                       $('#poll_sections').html(data.html);
                    }
                }
            },
            error: function(data) {
                // Swal.fire("Oops! Something went wrong. Please try again.", "", "error");
            }
        });
    }

    /* Socket update */
    socket.on(`poll-added`, function(data) {
        let votes = jQuery.parseJSON(data);
        generatePollCard(votes,true)
    });
    socket.on(`poll-votes`, function(data) {
        let votes = jQuery.parseJSON(data);
        generatePollCard(votes)
    });
    socket.on(`poll-delete`, function(data) {
        let votes = jQuery.parseJSON(data);
        deletePoll(votes)
    });

    function generatePollCard(data, is_new = false) {
        var close_poll_title = (data.close == 1)? '<small class="text-danger"><i>This poll is closed</i></small>' : '';
        var first_div = (is_new)? `<div class="col-md-4" id="poll-card-${data.secret}">` : '';
        var end_div = (is_new)? `</div>` : '';

        let total_vote_count = data.answer_array['total_count'];
        let poll_percentage = data.answer_array.poll_percentage;
        let poll_option_count = data.answer_array.poll_option_count;
        let poll_option_html = "";
        let process_index = 1;

        data.poll_options.forEach(element => {
            var is_checked = (data.answer_array['data_array'][auth_id] !== undefined && data.answer_array['data_array'][auth_id] == element.id)? 'checked' : '';
            var checked_class = (data.answer_array['data_array'][auth_id] !== undefined && data.answer_array['data_array'][auth_id] == element.id)? 'checked-border my-2' : '';
            var is_closed = (data.close == 1)? 'disabled' : '';
            var percentage = (poll_percentage[element.id] !== undefined)? poll_percentage[element.id] : 0;
            var poll_answer_count = (poll_option_count[element.id] !== undefined)? poll_option_count[element.id] : 0;
            if (process_index > 5) {
                process_index = 1;
            }
            var process_class = progress_bar_classes[process_index];
            process_index++;

            var options = `
                    <div class="row ${checked_class}">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input  ${ (data.close == 0)? 'poll-answer' : '' }" type="radio" value="${element.secret}"
                                    data-poll_id="${data.secret}"
                                    name="option[${data.secret}]"
                                    ${is_checked}
                                    ${is_closed}
                                    id="option-${element.id}">
                                <label class="form-check-label" for="option-${element.id}">
                                    ${element.option}
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="progress">
                                <div class="progress-bar ${process_class}" role="progressbar" style="width: ${percentage}%" aria-valuenow="${total_vote_count}" aria-valuemin="0" aria-valuemax="100">${poll_answer_count}</div>
                            </div>
                        </div>
                    </div>`;
            poll_option_html += options;
        });

        var card = `
            ${first_div}
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">${data.question} ${close_poll_title}</h5>
                        <p class="mb-0"><strong>Vote Count:</strong> ${data.answer_array.total_count} </p>
                    </div>
                    <div class="card-body">
                        ${poll_option_html}
                    </div>
                </div>
            ${end_div}
        `;

        if(is_new) {
            $('#poll-list-section').prepend(card);
        } else {
            $('#poll-card-'+data.secret).html(card);
        }
    }

    function deletePoll(poll) {
        $('#poll-card-'+poll.secret).remove();
    }

    function initVote(){
        /* Vote on poll */
        $(document).on('change', '.poll-answer', function() {
            var id = $(this).data("poll_id");
            var poll_option_id = $(this).val();
            $.ajax({
                type: "post",
                url: submit_poll_url,
                async: false,
                data: { id: id, poll_option_id: poll_option_id },
                success: function(data) {
                    // if (typeof data !== "undefined") {
                    //     if (typeof data.status !== "undefined" && data.status == true) {
                    //         get_polls();
                    //     }
                    // }
                },
                error: function(data) {
                    // Swal.fire("Oops! Something went wrong. Please try again.", "", "error");
                }
            });
        });
    }
});
