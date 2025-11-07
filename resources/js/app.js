import './bootstrap';
import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;
window.Chart = Chart;
window.dispatchEvent(new Event('chart:ready'));

document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggler = document.getElementById('sidebar-toggler');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggler && sidebar) {
        sidebar.classList.add('sidebar-transition');

        sidebarToggler.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            document.cookie = 'sidebar_collapsed=' + sidebar.classList.contains('collapsed') + '; path=/';
        });
    }
});
