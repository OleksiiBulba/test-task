// jQuery's library is available by webpack, so we don't need to import it manually
// import $ from 'jquery'

import anychart from 'anychart';

$(document).ready(function () {
    if (0 === data.length) {
        return;
    }

    // create a chart
    const chart = anychart.candlestick();

    // set the interactivity mode
    chart.interactivity('by-x');

    // create a japanese candlestick series and set the data
    const series = chart.candlestick(data);
    series.pointWidth(600 / data.length);

    // set the chart title
    chart.title('Historical quotes');

    // set the titles of the axes
    chart.xAxis().title('Date');
    chart.yAxis().title('Price, $');

    // set the container id
    chart.container('container');

    // initiate drawing the chart
    chart.draw();
});
