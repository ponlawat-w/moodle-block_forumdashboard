require(['jquery'], $ => {
  const block_forumdashboard_initialze = $block => {
    const $scopeselect = $block.find('.block_forumdashboard_scopeselect');
    const $expandbtn = $block.find('.block_forumdashboard_expandbtn');
    const $initialmetricitems = $block.find('.block_forumdashboard_metricitem[data-initial=1]');
    const $hiddenmetricitems = $block.find('.block_forumdashboard_metricitem[data-initial!=1]');

    const resetcontent = () => {
      $hiddenmetricitems.hide();
      $block.find('.block_forumdashboard_metricitem .block_forumdashboard_metricitem_content').hide();
      $block.find('.block_forumdashboard_metricitem .block_forumdashboard_metricitem_loading').show();
      if ($hiddenmetricitems.length) {
        $expandbtn.show();
      }
      loaddata($initialmetricitems);
    };

    const loaditem = $item => {
      const searchparams = new URLSearchParams({
        courseid: $scopeselect.val(),
        itemid: $item.attr('data-itemid')
      });
      $.ajax({
        url: `${M.cfg.wwwroot}/blocks/forumdashboard/api.php?${searchparams.toString()}`,
        method: 'get'
      }).done(response => {
        if (response && !response.error) {
          $item.find('.block_forumdashboard_metricitem_content_value').html(response.valuetext ? response.valuetext : '');
          $item.find('.block_forumdashboard_metricitem_content_average').html(response.averagetext ? response.averagetext : '');
          $item.find('.block_forumdashboard_metricitem_content').show();
        }
      }).always(() => {
        $item.find('.block_forumdashboard_metricitem_loading').hide();
      });
    };
  
    const loaddata = $items => {
      for (let i = 0; i < $items.length; i++) {
        const $item = $($items[i]);
        loaditem($item);
      }
    };

    $expandbtn.click(() => {
      $expandbtn.hide();
      $hiddenmetricitems.show();
      loaddata($hiddenmetricitems);
    });

    $scopeselect.change(() => {
      resetcontent();
    });

    $hiddenmetricitems.hide();
    loaddata($initialmetricitems);
  };

  $(document).ready(() => {
    $('.block_forumdashboard_block').each((_, block) => {
      block_forumdashboard_initialze($(block));
    });
  });
});
