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
 * JS script of admin page
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(['jquery'], $ => {
    $(document).ready(() => {
        const $table = $('#block_forumdashboard_table');
        const $data = $('#block_forumdashboard_configdata');
        const $newitem = $('#block_forumdashboard_newitem');
        const $newinitial = $('#block_forumdashboard_newinitial');
        const $newshowaverage = $('#block_forumdashboard_newshowaverage');
        const $newcaching = $('#block_forumdashboard_newcaching');
        const $addbtn = $('#block_forumdashboard_addbtn');
        const metricItems = JSON.parse($('#block_forumdashboard_metricitems').val());
        const metricItemInstances = JSON.parse($data.val());

        const getMetricItemProperty = (name, property) => {
            const filteredMetricItems = metricItems.filter(m => m.name === name);
            return filteredMetricItems.length && filteredMetricItems[0][property] ? filteredMetricItems[0][property] : null;
        };

        const getInputData = $element => {
            switch ($element.attr('data-type')) {
                case 'text': return $element.val();
                case 'bool': return $element.prop('checked');
            }
            return null;
        };

        const inputChange = e => {
            const $target = $(e.target);
            const index = parseInt($target.closest('tr').attr('data-index'));

            metricItemInstances[index][$target.attr('data-target')] = getInputData($target);
            $data.val(JSON.stringify(metricItemInstances));
            update();
        };

        const actionSwitchIndex = (index1, index2) => {
            if (metricItemInstances[index1] && metricItemInstances[index2]) {
                const temp = metricItemInstances[index1];
                metricItemInstances[index1] = metricItemInstances[index2];
                metricItemInstances[index2] = temp;
            }
        };
        const actionRemove = index => {
            if (confirm(M.util.get_string('confirmremove', 'block_forumdashboard'))) {
                metricItemInstances.splice(index, 1);
            }
        };

        const actionClick = e => {
            const $target = e.target.tagName.toLowerCase() === 'a' ? $(e.target) : $(e.target).closest('a');
            const index = parseInt($target.closest('tr').attr('data-index'));

            switch ($target.attr('data-action')) {
                case 'moveup': actionSwitchIndex(index, index - 1); break;
                case 'movedown': actionSwitchIndex(index, index + 1); break;
                case 'remove': actionRemove(index); break;
            }

            update();
        };

        const update = () => {
            $data.val(JSON.stringify(metricItemInstances));

            if (!metricItemInstances.length) {
                $table.hide(); return;
            } else {
                $table.show();
            }

            const $tbody = $table.find('tbody');
            $tbody.find('.block_forumdashboard_tr').remove();
            const $prototype = $table.find('#block_forumdashboard_prototypetr');
            for (let i = 0; i < metricItemInstances.length; i++) {
                const instance = metricItemInstances[i];
                const $tr = $prototype.clone().attr('id', '').attr('data-index', i).addClass('block_forumdashboard_tr');
                $tr.find('.block_forumdashboard_nametd')
                    .html(getMetricItemProperty(instance.item, 'namestr'))
                    .css('background-color', instance.bgcolor ? instance.bgcolor : getMetricItemProperty(instance.item, 'default_bgcolor'))
                    .css('color', instance.textcolor ? instance.textcolor : getMetricItemProperty(instance.item, 'default_textcolor'));
                $tr.find('input[data-target=initial]').prop('checked', instance.initial ? true : false);
                $tr.find('input[data-target=showaverage]').prop('checked', instance.showaverage ? true : false);
                $tr.find('input[data-target=caching]').prop('checked', instance.caching ? true : false);
                $tr.find('input[data-target=bgcolor]').val(instance.bgcolor);
                $tr.find('input[data-target=textcolor]').val(instance.textcolor);

                $tr.find('input').change(inputChange);
                $tr.find('.block_forumdashboard_actionbtn').click(actionClick);

                $tr.appendTo($tbody).show();
            }
        };

        const registerCssUpdate = (selector, cssPropertyName) => {
            $(selector).on('change', e => {
                $target = $(e.target);
                $target.closest('tr').find('.block_forumdashboard_nametd').css(cssPropertyName, $target.val());
            });
        };

        const getid = itemName => {
            let i = 0;
            while (true) {
                const newid = `${itemName}_${++i}`;
                if (!metricItemInstances.filter(m => m.id === newid).length) {
                    return newid;
                }
            }
        };

        update();
        registerCssUpdate('input[data-target=bgcolor]', 'background-color');
        registerCssUpdate('input[data-target=textcolor]', 'color');

        $addbtn.click(() => {
            const item = $newitem.val();
            metricItemInstances.push({
                id: getid(item),
                item: item,
                initial: $newinitial.prop('checked'),
                showaverage: $newshowaverage.prop('checked'),
                caching: $newcaching.prop('checked')
            });
            update();
        });
    });
});
