$(function() {
	"use strict";

    let originalButtonText;

    

    $("#userForm").on("submit", function(e) {
        e.preventDefault();
        const form = $(this);
        const formArray = {
            formId: form.attr("id"),
            csrf: form.find('input[name="_token"]').val(),
            url: form.closest('form').attr('action'),
            formObject : form,
        };
        ajaxRequest(formArray);
    });

    function disableSubmitButton(form) {
        originalButtonText = form.find('button[type="submit"]').text();
        form.find('button[type="submit"]')
            .prop('disabled', true)
            .html('  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...');
    }

    function enableSubmitButton(form) {
        form.find('button[type="submit"]')
            .prop('disabled', false)
            .html(originalButtonText);
    }

    function beforeCallAjaxRequest(formObject){
        disableSubmitButton(formObject);
    }
    function afterCallAjaxResponse(formObject){
        enableSubmitButton(formObject);
    }
    function afterSeccessOfAjaxRequest(formObject){
        formAdjustIfSaveOperation(formObject);
        pageRedirect(formObject);
    }

    function pageRedirect(formObject){
        var redirectTo = '/users/list';
        setTimeout(function() { 
           location.href = baseURL + redirectTo;
        }, 1000);
    }
    function ajaxRequest(formArray){
        var formData = new FormData(document.getElementById(formArray.formId));
        var jqxhr = $.ajax({
            type: 'POST',
            url: formArray.url,
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': formArray.csrf
            },
            beforeSend: function() {
                // Actions to be performed before sending the AJAX request
                if (typeof beforeCallAjaxRequest === 'function') {
                    beforeCallAjaxRequest(formArray.formObject);
                }
            },
        });
        jqxhr.done(function(data) {
            iziToast.success({title: 'Success', layout: 2, message: data.message});
            // Actions to be performed after response from the AJAX request
            if (typeof afterSeccessOfAjaxRequest === 'function') {
                afterSeccessOfAjaxRequest(formArray.formObject);
            }
        });
        jqxhr.fail(function(response) {
                var message = response.responseJSON.message;
                iziToast.error({title: 'Error', layout: 2, message: message});
        });
        jqxhr.always(function() {
            // Actions to be performed after the AJAX request is completed, regardless of success or failure
            if (typeof afterCallAjaxResponse === 'function') {
                afterCallAjaxResponse(formArray.formObject);
            }
        });
    }

    function formAdjustIfSaveOperation(formObject){
        const _method = formObject.find('input[name="_method"]').val();
        /* Only if Save Operation called*/
        if(_method.toUpperCase() == 'POST' ){
            formObject.find('input[name="name"]').val('');
        }
    }

    /**
     * Select All
     * */
    $("#select_all").on("click", function () {
        var checkBox = $(this).prop("checked");

        $(".row-select").prop("checked", checkBox);

        $(".row-select").each(function() {
            //Permission checkbox class name

            var permissionClass = $(this).attr("id");

            $("."+permissionClass+"_p").prop("checked", checkBox);

        });
    });

    /**
     * Group Checkbox operation
     * */
    $(".row-select").on("click", function () {
        var checkBox = $(this).prop("checked");

        var groupClassName = $(this).attr("id")

        //Multiple
        $("."+groupClassName+"_p").each(function() {

            $(this).prop("checked", checkBox);

        });
    });

    /**
     * Image Browse & Reset
     * */
    function loadImageBrowser(uploadedImage, accountFileInput, accountImageReset) {
        if (uploadedImage.length) {
            const avatarSrc = uploadedImage.attr("src");

            accountFileInput.on("change", function() {
              if (accountFileInput[0].files[0]) {

                uploadedImage.attr("src", window.URL.createObjectURL(accountFileInput[0].files[0]));
              }
            });

            accountImageReset.on("click", function() {
              accountFileInput[0].value = "";
              uploadedImage.attr("src", avatarSrc);
            });
        }

    }

    $(document).ready(function() {
        loadImageBrowser($("#uploaded-image-1"), $(".input-box-class-1"), $(".image-reset-class-1"));

        showWarehouseSelection();
    });

    /**
     * Show or Hide Warehouse Selection
     * */
    $(document).on('change', 'input[name="is_allowed_all_warehouses"]', function() {
        showWarehouseSelection();
    });

    function showWarehouseSelection() {
        var isAllowedAllWarehouses = $('input[name="is_allowed_all_warehouses"]:checked').val();
        var btn = '';
        if(isAllowedAllWarehouses){
            $(".warehouse-div").hide();
        }else{
            $(".warehouse-div").show();
        }
        // $(".trackBtn").remove();
        // $("input[name='opening_quantity']").after(btn);
    }
});//main function
