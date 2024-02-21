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
        // if($(".question_options").length){
        //     $(".question_options").each(function () {
        //         console.log($(this));
        //         $(this).rules("add", {
        //             required:true,
        //             min_options: true,
        //             normalizer: function(value) { return $.trim(value); },
        //             messages: {
        //                 required: "Please enter option.",
        //             }
        //         });
        //     });
        // }
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
                { data: 'question', name: 'question' },
                { data: 'action', name: 'action' },
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
});
