@php $custom = Cache::get('customization') @endphp
<style>
    .left-side-menu-dark .navbar-custom{
        background-color : #{{ $custom->top_bar ?? '#2c94f2' }} !important;
    }
    .left-side-menu{
        background-color: #{{ $custom->side_bar ?? '#38414a' }} !important;
    }
    .footer{
        background-color:  #{{ $custom->footer ?? '#eeeff3' }} !important;
    }
    .primary{
        background-color:  #{{ $custom->primary ?? '#2c94f2' }} !important;
    }
    .primary-hover{
        background-color:  #{{ $custom->primary_hover ?? '#1479B8' }} !important;
    }
    .success{
        background-color:  #{{ $custom->success ?? '#1ABB9C' }} !important;
    }
    .success-hover{
        background-color:  #{{ $custom->success_hover ?? '#19A98B' }} !important;
    }
    .info{
        background-color:  #{{ $custom->info ?? '#31b0d5' }} !important;
    }
    .info-hover{
        background-color:  #{{ $custom->info_hover ?? '#4FB5D3' }} !important;
    }
    .warning{
        background-color:  #{{ $custom->warning ?? '#ec971f' }} !important;
    }
    .warning-hover{
        background-color:  #{{ $custom->warning_hover ?? '#d58512' }} !important;
    }
    .danger{
        background-color:  #{{ $custom->danger ?? '#ec971f' }} !important;
    }
    .danger-hover{
        background-color:  #{{ $custom->danger_hover ?? '#d58512' }} !important;
    }
    *{
        color:  #{{ $custom->fonts ?? '#73879C' }} !important;
    }
    a{
        color:  #{{ $custom->links ?? '#5A738E' }} !important;
    }
    a:hover{
        color:  #{{ $custom->links_hover ?? '#5A738E' }} !important;
    }
    

</style>