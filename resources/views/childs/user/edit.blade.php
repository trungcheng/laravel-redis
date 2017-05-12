<form class="form-horizontal" id="formEditUser" action="/user/edit" method="post">
    <input type="hidden" name="uid" value="{{ $user->id }}"/>
    <div class="form-group">
        <label for="inputuser_type"
               class="col-sm-2 control-label">{{ trans('user.user_type') }}</label>
        <div class="col-sm-10">
            <select class="form-control" name="user_type">
                @foreach(config('admincp.user_type') as $item)
                    <option value="{{ $item }}" {{ $item == $user->user_type ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputName"
               class="col-sm-2 control-label">{{ trans('user.name') }}</label>
        <div class="col-sm-10">
            <input type="name" class="form-control" name="name"
                   value="{{ $user->name }}"
                   placeholder="{{ trans('user.name_placeholder') }}">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail"
               class="col-sm-2 control-label">{{ trans('user.email') }}</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email"
                   value="{{ $user->email }}"
                   placeholder="{{ trans('user.email_placeholder') }}">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword"
               class="col-sm-2 control-label">{{ trans('user.password') }}</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password"
                   placeholder="{{ trans('user.enter_password') }}"
                   autocomplete="new-password">
        </div>
    </div>
    <div class="form-group">
        <label for="inputDescription"
               class="col-sm-2 control-label">{{ trans('user.description') }}</label>
        <div class="col-sm-10"><textarea class="form-control" name="description"
                                         placeholder="{{ trans('user.description_placeholder') }}">{{ $user->description }}</textarea>
        </div>
    </div>

    <div class="box-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit"
                class="btn btn-success pull-right">{{ trans('user.change') }}</button>
    </div>
</form>

<script>
    $(function () {
        UserModule.initSelectZone();

        $(".select2").select2();

        $("#formEditUser").submit(function (event) {
            event.preventDefault();
            $("#formCreateUser").empty();
            var i, r, o, p;
            var t = $('select[name=user_type]').val();
            if (t == UserType.type_2 || t == UserType.type_3) {
                i = $('select[name=media_zone]').val();
                r = $('select[name=sms_zone]').val();
            } else if (t == UserType.type_4) {
                o = $('select[name=report_zone').val();
            } else if (t == UserType.type_5) {
                p = $('select[name=search_zone]').val();
            }

            var re = /^([0-9a-zA-Z]([-_\\.]*[0-9a-zA-Z]+)*)@([0-9a-zA-Z]([-_\\.]*[0-9a-zA-Z]+)*)[\\.]([a-zA-Z]{2,9})$/;

            var formData = {
                user_type: t,
                media_zone: !i ? '' : i,
                sms_zone: !r ? '' : r,
                report_zone: !o ? '' : o,
                search_zone: !p ? '' : p,
                uid: $('input[name=uid]').val(),
                name: $('input[name=name]').val() === '' ? swal(AdminCPLang.lang_13) : $('input[name=name]').val(),
                email: $('input[name=email]').val() === '' || !re.test($('input[name=email]').val()) ? swal(AdminCPLang.lang_14) : $('input[name=email]').val(),
                password: ($('input[name=password]').val().length < 6 && $('input[name=password]').val().length > 0) ? swal(AdminCPLang.lang_15) : $('input[name=password]').val(),
                description: $('textarea[name=description]').val()
            }

            if (!formData.name || !formData.email || formData.password === undefined) {
                return;
            }

            $.ajax({
                        type: "POST",
                        url: '/user/edit',
                        dataType: 'json',
                        data: formData, // serializes the form's elements.
                        success: function (res) {
                            swal({title: res.msg, type: res.status}, function () {
                                UserModule.initUserDatatable();
                                $("#editUserModal").modal('hide');
                            });
                        }
                    }
            );
        });
    });
</script>