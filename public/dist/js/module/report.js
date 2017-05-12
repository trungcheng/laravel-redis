var ReportModule = function () {
    var a = function () {
        $("#smsFormReport").submit(function (e) {
            e.preventDefault();
            var s = $("#short_code").val();
            var st = $("#start_date").val();
            var d = $("#end_date").val();

            if (st === '' || d === '') {
                swal({title: AdminCPLang.lang_17, type: 'error'});
                return;
            }
            if (s === '') {
                swal({title: AdminCPLang.lang_18, type: 'error'});
                return;
            }
            var url = '/report/sms?' + $(this).serialize();
            if (url !== window.location.pathname + window.location.search) {
                location.href = url;
            } else {
                swal({title: AdminCPLang.lang_19, 'type': 'error'});
            }
        });
    };

    return {
        initSMS: function () {
            a()
        }
    }
}();