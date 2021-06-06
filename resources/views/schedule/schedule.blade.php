@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="calendar col-sm-10 col-md-8 col-lg-4">
            
            @if (session('flash_msg'))
            <div class="flash-message">
                {{ session('flash_msg') }}
            </div><!-- flash-message -->
            @endif

            <div class="card">
                <div class="card-header text-center">
                    <a class="btn btn-outline-secondary float-left" href="{{ url('/schedule?date=' . $calendar->getPreviousMonth()) }}">◀︎</a>
                    <h2>{{ $calendar->getTitle() }}</h2>
                    <a class="btn btn-outline-secondary float-right" href="{{ url('/schedule?date=' . $calendar->getNextMonth()) }}">▶︎</a>
                </div><!-- card-header -->

                <div class="card-body">
                    {!! $calendar->render() !!}
                </div><!-- card-body -->
            </div><!-- card -->

            <p class="description">日付をクリックして予定を登録できます</p>
        </div><!-- calendar -->

        <div class="plan-list col-lg-8 col-md-10 col-lg-7">
            <h2>予定一覧</h2>
            <ul>{!! $plan_view->getTagColor() !!}</ul>

            {!! $plan_view->getPlanView() !!}
        </div><!-- plan-list -->
    </div><!-- row -->
</div><!-- container -->
@endsection


@section('script')
<script>
$(function(){
    $('.plan-main').on('click', function(){
        $(this).next().slideToggle();
        $(this).children('i').toggleClass('open');
    });

    function initialize() {
        $('.selected').each(function(){
            $(this).removeClass('selected');
        });
        $('.hide').each(function(){
            $(this).removeClass('hide');
        });
        $('.show').each(function(){
            $(this).removeClass('show');
        });
        $('.plans-oneday').each(function(){
            $('.plans-oneday').show();
        });
        $('.plan-wrap').each(function(){
            $('.plan-wrap').show();
        });
    }

    var select_id = '';
    function selected(select_id){
        $(select_id).toggleClass('selected');
    }

    var show_class = '';
    function changeShow(show_class){
        $(this).show();
        $(this).toggleClass('show');
    }

    var hide_class = '';
    function changeHide(hide_class){
        $(hide_class).hide();
        $(hide_class).toggleClass('hide');
    }

    var hide_class_array = [];
    function eachChangeHide(hide_class_array){
        $(hide_class_array).each(function(){
            changeHide(this);
        });
    }

    function oneday_hide(){
        $('.plans-oneday').each(function(){
            var class_name = [];
            var counter_child = 0;
            var counter_hide = 0;
            var search = 'hide';

            $(this).children('div').each(function(){
                $(this).each(function(){
                    class_name = $(this).attr('class');
                    if(class_name.indexOf(search) > -1){
                        counter_hide++;
                    }
                });
                counter_child++;
            });

            if(counter_hide == counter_child){
                $(this).hide();
            }
        });
    }

    $('#pink').on('click', function(){
        initialize();

        selected($('#pink'));
        changeShow($('.pink'));

        hide_class_array = [$('.orange'),$('.palegreen'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#orange').on('click', function(){
        initialize();

        selected($('#orange'));
        changeShow($('.orange'));

        hide_class_array = [$('.pink'),$('.palegreen'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#palegreen').on('click', function(){
        initialize();

        selected($('#palegreen'));
        changeShow($('.palegreen'));

        hide_class_array = [$('.pink'),$('.orange'),$('.skyblue'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#skyblue').on('click', function(){
        initialize();

        selected($('#skyblue'));
        changeShow($('.skyblue'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.tomato'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#tomato').on('click', function(){
        initialize();

        selected($('#tomato'));
        changeShow($('.tomato'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.skyblue'),$('.gray')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#gray').on('click', function(){
        initialize();

        selected($('#gray'));
        changeShow($('.gray'));

        hide_class_array = [$('.pink'),$('.orange'),$('.palegreen'),$('.skyblue'),$('.tomato')];
        eachChangeHide(hide_class_array);

        oneday_hide();
    });

    $('#all').on('click', function(){
        initialize();
        selected($('#all'));
    });
});
</script>
@endsection