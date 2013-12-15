/**
 * Copyright © 2013 The Galette Team
 *
 * This file is part of Galette (http://galette.tuxfamily.org).
 *
 * Galette is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Galette is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Galette. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Javascript
 * @package   Galette
 *
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 */

L.Control.SidebarBtn = L.Control.extend({
    options: {
        sidebar: null,
        position: 'topright',
        strings: {
            title: "Show/Hide sidebar"
        }
    },

    onAdd: function (map) {
        var container = L.DomUtil.create('div',
            'leaflet-control-sidebarbtn leaflet-bar leaflet-control');

        var link = L.DomUtil.create('a', 'leaflet-bar-part leaflet-bar-part-single', container);
        link.href = '#';
        link.title = this.options.strings.title;
        var sidebar = this.options.sidebar;

        L.DomEvent
            .on(link, 'click', L.DomEvent.stopPropagation)
            .on(link, 'click', L.DomEvent.preventDefault)
            .on(link, 'click', function() {
                sidebar.toggle();
            })
            .on(link, 'dblclick', L.DomEvent.stopPropagation);

        return container;
    }
});

L.Map.addInitHook(function () {
    if (this.options.sidebarbtnControl) {
        this.sidebarbtnControl = L.control.sidebarbtn();
        this.addControl(this.sidebarbtnControl);
    }
});

L.control.sidebarbtn = function (options) {
    return new L.Control.SidebarBtn(options);
};
