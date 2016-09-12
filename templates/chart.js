var dataset = [
    { label: 'Abulia', count: "10" },
    { label: 'Betelgeuse', count: "20"},
    { label: 'Cantaloupe', count: "30"},
    { label: 'Dijkstra', count: "40" }
];

drawPieChart(dataset);

function drawPieChart(dataset) {
  'use strict';

  var width = 360;
  var height = 360;
  var radius = Math.min(width, height) / 2;
  var donutWidth = 75;
  var legendRectSize = 18;
  var legendSpacing = 4;

  var color = d3.scale.category20b();

  var svg = d3.select('#chart')
    .append('svg')
    .attr('width', width)
    .attr('height', height)
    .append('g')
    .attr('transform', 'translate(' + (width / 2) +
      ',' + (height / 2) + ')');

  var arc = d3.svg.arc()
    .innerRadius(radius - donutWidth)
    .outerRadius(radius);

  var pie = d3.layout.pie()
    .value(function(d) { return d.count; })
    .sort(null);

  var tooltip = d3.select('#chart')                               // NEW
    .append('div')                                                // NEW
    .attr('class', 'tooltip');                                    // NEW

  tooltip.append('div')                                           // NEW
    .attr('class', 'label');                                      // NEW

  tooltip.append('div')                                           // NEW
    .attr('class', 'count');                                      // NEW

  tooltip.append('div')                                           // NEW
    .attr('class', 'percent');                                    // NEW

    var path = svg.selectAll('path')
      .data(pie(dataset))
      .enter()
      .append('path')
      .attr('d', arc)
      .attr('fill', function(d, i) {
        return color(d.data.label);
      });

    path.on('mouseover', function(d) {                            // NEW
      var total = d3.sum(dataset.map(function(d) {                // NEW
        return d.count;                                           // NEW
      }));                                                        // NEW
      var percent = Math.round(1000 * d.data.count / total) / 10; // NEW
      tooltip.select('.label').html(d.data.label);                // NEW
      tooltip.select('.count').html(d.data.count);                // NEW
      tooltip.select('.percent').html(percent + '%');             // NEW
      tooltip.style('display', 'block');                          // NEW
    });                                                           // NEW

    path.on('mouseout', function() {                              // NEW
      tooltip.style('display', 'none');                           // NEW
    });                                                           // NEW

    path.on('click', function(d) {                              // NEW
      //alert("click "+d.data.label+" "+d.data.count);

      // add ajax to query data

      dataset = [
            { label: 'isaac', count: "40" },
            { label: 'Betelgeuse', count: "20"},
            { label: 'Cantaloupe', count: "30"},
            { label: 'Dijkstra', count: "40" }
      ];
      d3.select('#chart').html("");
      drawPieChart(dataset);

    });                                                           // NEW

    /* OPTIONAL
    path.on('mousemove', function(d) {                            // NEW
      tooltip.style('top', (d3.event.pageY + 10) + 'px')          // NEW
        .style('left', (d3.event.pageX + 10) + 'px');             // NEW
    });                                                           // NEW
    */

    var legend = svg.selectAll('.legend')
      .data(color.domain())
      .enter()
      .append('g')
      .attr('class', 'legend')
      .attr('transform', function(d, i) {
        var height = legendRectSize + legendSpacing;
        var offset =  height * color.domain().length / 2;
        var horz = -2 * legendRectSize;
        var vert = i * height - offset;
        return 'translate(' + horz + ',' + vert + ')';
      });

    legend.append('rect')
      .attr('width', legendRectSize)
      .attr('height', legendRectSize)
      .style('fill', color)
      .style('stroke', color);

    legend.append('text')
      .attr('x', legendRectSize + legendSpacing)
      .attr('y', legendRectSize - legendSpacing)
      .text(function(d) { return d; });


}
