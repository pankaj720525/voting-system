if (typeof filter_url === "undefined") { var filter_url = ""; }
if (typeof delete_url === "undefined") { var delete_url = ""; }
if (typeof exists_url === "undefined") { var exists_url = ""; }
$(document).ready(function() {

    $(document).on('click', '.add-option', function() {
        var uid = getRandomIndex();
        var poll_options = `
            <div class="mb-3 option-div-${uid}">
                <div class="input-group">
                    <input type="text" class="form-control question_options" name="option[${uid}]" placeholder="Enter Option" data-error=".option_error${uid}">
                    <div class="input-group-append">
                        <button class="btn btn-danger remove-option" data-class=".option-div-${uid}" type="button">Remove</button>
                    </div>
                </div>
                <div class="option_error${uid}"></div>
            </div>`;
        $('.poll-options').append(poll_options);

        extraValidation()
    });

    $(document).on('click', '.remove-option', function() {
        var dynamic_class = $(this).data('class');
        Swal.fire({
            title: "Do you want remove this option?",
            showCancelButton: true,
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $(dynamic_class).remove();
                Swal.fire("Removed!", "", "success");
            }
        });
    });

    function getRandomIndex() {
        var len = 8;
        return Math.random().toString(36).substring(2, len + 2);
    }

    /* Table JS */
    if($('.data-table').length){
        var table = $('.data-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            "bLengthChange": false,
            ajax: {
                type: "POST",
                url: filter_url,
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'question', name: 'question' },
                { data: 'action', name: 'action', class: 'text-center' },
            ]
        });
    }

    if (typeof validate !== 'function' && $.fn.validate && $(".form-validate").length > 0) {

        function extraValidation() {
            $(".question_options").each(function (item) {
                $(this).rules("add", {
                    required: true,
                });
            });
        }

        initValidation();

        function initValidation() {
            $(".form-validate").validate({
                rules: {
                    question: {
                        required: true,
                        minlength: 4,
                        normalizer: function(value) { return $.trim(value); },
                        remote: {
                            type: "POST",
                            url: exists_url,
                            data: {
                                id: $('input[name="id"]').val(),
                            }
                        },
                    },
                    "option[0]":{
                        required:true,
                    },
                    "option[1]":{
                        required:true,
                    }
                },
                messages: {
                    question: {
                        required: "Please enter question.",
                        remote: "This poll already exists!"
                    },
                    "option[0]":{
                        required: "Please enter option."
                    },
                    "option[1]":{
                        required: "Please enter option."
                    }
                },
                errorPlacement: function(error, element) {
                    var placement = $(element).data('error');
                    if (placement) {
                        $(placement).append(error);
                    } else {
                        error.insertAfter(element)
                    }
                }
            })
        }
    }

    $(document).on('click', '.delete', function() {
        var data_id = $(this).data("id");
        Swal.fire({
            title: "Are you sure!",
            text: "You want to delete this poll?",
            showCancelButton: true,
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "delete",
                    url: delete_url,
                    data: { id: data_id },
                    success: function(data) {
                        if (typeof data !== "undefined") {
                            if (typeof data.status !== "undefined" && data.status == true) {
                                table.ajax.reload();
                                Swal.fire(data.message, "", "success");
                            } else {
                                Swal.fire(data.message, "", "error");
                            }
                        } else {
                            Swal.fire("Oops! Something went wrong. Please try again.", "", "error");
                        }
                    },
                    error: function(data) {
                        Swal.fire("Oops! Something went wrong. Please try again.", "", "error");
                    }
                });
            }
        });
    });

    if (typeof socket !== "undefined") {
        socket.on(`poll-votes`, function(data) {
            let votes = jQuery.parseJSON(data);
            if (votes.id == poll_id) {
                result_html(votes);
            }
        });

        function result_html(poll){
            var body = "";
            var result = "";
            if (poll.poll_answer != null) {
                let poll_option_count = poll.answer_array.poll_option_count;
                poll.poll_answer.forEach(function(val, element) {
                    var poll_options = ``;
                    poll.poll_options.forEach(function(row, i) {
                        var poll_answer_count = (val.poll_option_id == row.id)? 1 : 0;
                        poll_options += `<td>${poll_answer_count}</td>`;
                    });
                    body += `<tr>
                        <td>${val.user_detail.name}</td>
                        ${poll_options}
                    </tr>`;
                });

                poll.poll_options.forEach(function(row, i) {
                    var poll_answer_count = (poll_option_count[row.id] !== undefined)? poll_option_count[row.id] : 0;
                    result += `<td>${poll_answer_count}</td>`;
                });
                body += `
                    <tr class="table-primary">
                        <td class="text-end fw-bold">Result:</td>
                        ${result}
                    </tr>
                `;
            } else {
                body = `
                    <tr>
                        <td class="text-center" colspan="{{ count($poll->poll_options) }}">No data found.</td>
                    </tr>
                `;
            }

            $('#result-content-body').html(body);
        }
    }
});
