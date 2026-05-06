import Alpine from "alpinejs";
import Chart from "chart.js/auto";
import jsVectorMap from "jsvectormap";
import "jsvectormap/dist/maps/world.js";
import "jsvectormap/dist/jsvectormap.min.css";

window.Chart = Chart;
window.jsVectorMap = jsVectorMap;
window.Alpine = Alpine;

Alpine.start();
