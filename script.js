require(['jquery'], $ => {
  const block_forumdashboard_initialze = $block => {
    const $scopeselect = $block.find('.block_forumdashboard_scopeselect');
    const $expandbtn = $block.find('.block_forumdashboard_expandbtn');
    const $initiallivemetricitems = $block.find('.block_forumdashboard_metricitem[data-initial=1][data-caching!=1]');
    const $hiddenlivemetricitems = $block.find('.block_forumdashboard_metricitem[data-initial!=1][data-caching!=1]');
    const $hiddenmetricitems = $block.find('.block_forumdashboard_metricitem[data-initial!=1]');
    const $lastupdated = $block.find('.block_forumdashboard_lastupdated');
    let expanded = false;

    const resetcontent = () => {
      $block.find('.block_forumdashboard_metricitem .block_forumdashboard_metricitem_content').hide();
      $block.find('.block_forumdashboard_metricitem .block_forumdashboard_metricitem_loading').show();
      loadliveitems($initiallivemetricitems);
      if (expanded) {
        loadliveitems($hiddenlivemetricitems);
      }
      loadcachingitems();
    };

    const applyresponseitem = ($item, responseitem) => {
      if (responseitem.value === null) {
        $item.find('.block_forumdashboard_metricitem_notavailable').show();
        $item.find('.block_forumdashboard_metricitem_content').hide();
      } else {
        $item.find('.block_forumdashboard_metricitem_notavailable').hide();
        $item.find('.block_forumdashboard_metricitem_content_value').html(responseitem.valuetext ? responseitem.valuetext : '');
        $item.find('.block_forumdashboard_metricitem_content_average').html(responseitem.averagetext ? responseitem.averagetext : '');
        $item.find('.block_forumdashboard_metricitem_content').show();
      }
    };

    const loadliveitem = $item => {
      const searchparams = new URLSearchParams({
        action: 'get',
        courseid: $scopeselect.val(),
        itemid: $item.attr('data-itemid')
      });
      $.ajax({
        url: `${M.cfg.wwwroot}/blocks/forumdashboard/api.php?${searchparams.toString()}`,
        method: 'get'
      }).done(response => {
        if (response && !response.error) {
          applyresponseitem($item, response);
        } else {
          $item.find('.block_forumdashboard_metricitem_notavailable').show();
          $item.find('.block_forumdashboard_metricitem_content').hide();
        }
      }).always(() => {
        $item.find('.block_forumdashboard_metricitem_loading').hide();
      });
    };
  
    const loadliveitems = $items => {
      for (let i = 0; i < $items.length; i++) {
        const $item = $($items[i]);
        loadliveitem($item);
      }
    };

    const loadcachingitems = () => {
      const searchparams = new URLSearchParams({
        action: 'getcached',
        courseid: $scopeselect.val()
      });
      $.ajax({
        url: `${M.cfg.wwwroot}/blocks/forumdashboard/api.php?${searchparams.toString()}`,
        method: 'get'
      }).done(response => {
        if (response && !response.error) {
          $lastupdated.html(response.lastcalculatedtext);
          for (const responseitem of response.items) {
            const $item = $block.find(`.block_forumdashboard_metricitem[data-itemid=${responseitem.itemid}]`);
            if ($item.length) {
              applyresponseitem($item, responseitem);
            }
          }
        } else {
          $block.find('.block_forumdashboard_metricitem[data-caching=1] .block_forumdashboard_metricitem_notavailable').show();
          $block.find('.block_forumdashboard_metricitem[data-caching=1] .block_forumdashboard_metricitem_content').hide();
        }
      }).always(() => {
        $block.find('.block_forumdashboard_metricitem[data-caching=1] .block_forumdashboard_metricitem_loading').hide();
      });
    };

    $expandbtn.click(() => {
      $expandbtn.hide();
      $hiddenmetricitems.show();
      loadliveitems($hiddenlivemetricitems);
      expanded = true;
    });

    $scopeselect.change(() => {
      resetcontent();
    });

    $hiddenmetricitems.hide();
    loadliveitems($initiallivemetricitems);
    loadcachingitems();
  };

  $(document).ready(() => {
    $('.block_forumdashboard_block').each((_, block) => {
      block_forumdashboard_initialze($(block));
    });
  });
});
