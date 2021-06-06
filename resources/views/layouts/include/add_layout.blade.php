<style>
    .plan-confirm .content-wrap .tag{
      line-height: {{ $layout_set->getLayout()['height_content'] }};
    }

    .plan-confirm .content-wrap .content{
      height: {{$layout_set->getLayout()['height_content']}};
    }

    .plan-confirm .content-wrap .tag,
    .plan-confirm .detail-wrap .tag {
      background-color: {{ $layout_set->getLayout()['tag_color'] }};
    }

    .plan-confirm .detail-wrap .detail{
      display: {{ $layout_set->getLayout()['display_detail'] }};
    }
</style>