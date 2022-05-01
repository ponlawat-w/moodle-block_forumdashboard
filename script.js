// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * JS script of main dashboard page
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(['jquery'], $ => {
    const block_forumdashboard_initialze = $block => {
        const $scopeselect = $block.find('.block_forumdashboard_scopeselect');
        const $expandbtn = $block.find('.block_forumdashboard_expandbtn');
        const $initiallivemetricitems = $block.find('.block_forumdashboard_metricitem[data-initial=1][data-caching!=1]');
        const $hiddenlivemetricitems = $block.find('.block_forumdashboard_metricitem[data-initial!=1][data-caching!=1]');
        const $hiddenmetricitems = $block.find('.block_forumdashboard_metricitem[data-initial!=1]');
        const $lastupdated = $block.find('.block_forumdashboard_lastupdated');
        const $notifications = $block.find('.block_forumdashboard_notifications');
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

        const updatenotifications = () => {
            const courseid = $scopeselect.val();
            if (parseInt(courseid) > 0) {
                $notifications.find(`.block_forumdashboard_notification[data-course!=${courseid}]`).hide();
                $notifications.find(`.block_forumdashboard_notification[data-course=${courseid}]`).show();
            } else {
                $notifications.find('.block_forumdashboard_notification').show();
            }

            $notifications.show();
            const $shownnotifications = $notifications.find('.block_forumdashboard_notification:visible');
            if (!$shownnotifications.length) {
                $notifications.hide();
            }
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
            updatenotifications();
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
