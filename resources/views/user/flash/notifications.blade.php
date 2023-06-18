<div class="flash-notifications">
    @if(Session::has('success'))
        <div class="alert fade alert-success alert-dismissible show">
            <button type="button" class="close font__size-18" data-dismiss="alert">
                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
            </button>
            <i class="start-icon far fa-check-circle faa-tada animated"></i>
            <b>{{ Session::get('success') }}</b>
        </div>
        {{ Session::forget('success') }}
    @endif
    @if(session('status'))
        <div class="alert fade alert-success alert-dismissible show">
            <button type="button" class="close font__size-18" data-dismiss="alert">
                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
            </button>
            <i class="start-icon far fa-check-circle faa-tada animated"></i>
            <b>{{ Session::get('status') }}</b>
        </div>
        @if(session('status')=='passwords.reset')
            Fjalkalimi u rivendos me sukses
        @endif
    @endif
    @if(Session::has('info'))
        <div class="alert fade alert-info alert-dismissible show">
            <button type="button" class="close font__size-18" data-dismiss="alert">
                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
            </button>
            <i class="start-icon fa fa-info-circle faa-tada animated"></i>
            <b>{{ Session::get('info') }}</b>
        </div>
        {{ Session::forget('info') }}
    @endif
    @if(Session::has('warning'))
        <div class="alert fade alert-warning alert-dismissible show">
            <button type="button" class="close font__size-18" data-dismiss="alert">
                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
            </button>
            <i class="start-icon fa fa-exclamation-triangle faa-flash animated"></i>
            <b>{{ Session::get('warning') }}</b>
        </div>
        {{ Session::forget('warning') }}
    @endif
    @if(Session::has('error'))
        <div class="alert fade alert-danger alert-dismissible show">
            <button type="button" class="close font__size-18" data-dismiss="alert">
                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
            </button>
            <i class="start-icon far fa-times-circle faa-pulse animated"></i>
            <b>{{ Session::get('error') }}</b>
        </div>
        {{ Session::forget('error') }}
    @endif
</div>
<script>
    let flashNotificationsD = document.querySelector('.flash-notifications');
    document.addEventListener("DOMContentLoaded", () => {
        flashNotificationsD.classList.add('load');
        let flashNotifications = document.querySelectorAll('.flash-notifications .alert .close');
        flashNotifications.forEach(flashNotification => {
            flashNotification.addEventListener('click', e=>{
                e.target.parentElement.classList.add('remove');
            })
        })
    });
</script>