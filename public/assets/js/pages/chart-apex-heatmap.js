function generateData(a, e) {
  for (var t = 0, r = []; t < a; ) {
    var n = (t + 1).toString(),
      o = Math.floor(Math.random() * (e.max - e.min + 1)) + e.min;
    r.push({ x: n, y: o }), t++;
  }
  return r;
}
var colors = ["#727cf5"],
  dataColors = $("#basic-heatmap").data("colors"),
  options = {
    chart: { height: 380, type: "heatmap" },
    dataLabels: { enabled: !1 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      { name: " متریک  1", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  2", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  3", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  4", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  5", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک   6", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  7", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  8", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  9", data: generateData(20, { min: 0, max: 90 }) },
    ],
    xaxis: { type: "category" },
  },
  chart = new ApexCharts(document.querySelector("#basic-heatmap"), options);
function generateData(a, e) {
  for (var t = 0, r = []; t < a; ) {
    var n = (t + 1).toString(),
      o = Math.floor(Math.random() * (e.max - e.min + 1)) + e.min;
    r.push({ x: n, y: o }), t++;
  }
  return r;
}
chart.render();
(colors = [
  "#F3B415",
  "#F27036",
  "#663F59",
  "#6A6E94",
  "#4E88B4",
  "#00A7C6",
  "#18D8D8",
  "#A9D794",
  "#46AF78",
]),
  (dataColors = $("#multiple-series-heatmap").data("colors")),
  (options = {
    chart: { height: 380, type: "heatmap" },
    dataLabels: { enabled: !1 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      { name: " متریک  1", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  2", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  3", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  4", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  5", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  6", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  7", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  8", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک  9", data: generateData(20, { min: 0, max: 90 }) },
    ],
    xaxis: { type: "category" },
  });
function generateData(a, e) {
  for (var t = 0, r = []; t < a; ) {
    var n = (t + 1).toString(),
      o = Math.floor(Math.random() * (e.max - e.min + 1)) + e.min;
    r.push({ x: n, y: o }), t++;
  }
  return r;
}
(chart = new ApexCharts(
  document.querySelector("#multiple-series-heatmap"),
  options
)).render();
(colors = ["#fa6767", "#f9bc0d", "#44badc", "#42d29d"]),
  (dataColors = $("#color-range-heatmap").data("colors")),
  (options = {
    chart: { height: 380, type: "heatmap" },
    plotOptions: {
      heatmap: {
        shadeIntensity: 0.5,
        colorScale: {
          ranges: [
            {
              from: -30,
              to: 5,
              name: "پایین",
              color: (colors = dataColors ? dataColors.split(",") : colors)[0],
            },
            { from: 6, to: 20, name: "متوسط", color: colors[1] },
            { from: 21, to: 45, name: "بالا", color: colors[2] },
            { from: 46, to: 55, name: "شدید", color: colors[3] },
          ],
        },
      },
    },
    dataLabels: { enabled: !1 },
    series: [
      { name: "فروردین", data: generateData(20, { min: -30, max: 55 }) },
      { name: "اردیبهشت", data: generateData(20, { min: -30, max: 55 }) },
      { name: "خرداد", data: generateData(20, { min: -30, max: 55 }) },
      { name: "تیر", data: generateData(20, { min: -30, max: 55 }) },
      { name: "مرداد", data: generateData(20, { min: -30, max: 55 }) },
      { name: "شهریور", data: generateData(20, { min: -30, max: 55 }) },
      { name: "مهر", data: generateData(20, { min: -30, max: 55 }) },
      { name: "آبان", data: generateData(20, { min: -30, max: 55 }) },
      { name: "آذر", data: generateData(20, { min: -30, max: 55 }) },
    ],
  });
function generateData(a, e) {
  for (var t = 0, r = []; t < a; ) {
    var n = (t + 1).toString(),
      o = Math.floor(Math.random() * (e.max - e.min + 1)) + e.min;
    r.push({ x: n, y: o }), t++;
  }
  return r;
}
(chart = new ApexCharts(
  document.querySelector("#color-range-heatmap"),
  options
)).render();
(colors = ["#39afd1", "#0acf97"]),
  (dataColors = $("#rounded-heatmap").data("colors")),
  (options = {
    chart: { height: 380, type: "heatmap" },
    stroke: { width: 0 },
    plotOptions: {
      heatmap: {
        radius: 30,
        enableShades: !1,
        colorScale: {
          ranges: [
            {
              from: 0,
              to: 50,
              color: (colors = dataColors ? dataColors.split(",") : colors)[0],
            },
            { from: 51, to: 100, color: colors[1] },
          ],
        },
      },
    },
    colors: colors,
    dataLabels: { enabled: !0, style: { colors: ["#fff"] } },
    series: [
      { name: " متریک 1", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 2", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 3", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 4", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 5", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 6", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 7", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 8", data: generateData(20, { min: 0, max: 90 }) },
      { name: " متریک 8", data: generateData(20, { min: 0, max: 90 }) },
    ],
    xaxis: { type: "category" },
    grid: { borderColor: "#f1f3fa" },
  });
(chart = new ApexCharts(
  document.querySelector("#rounded-heatmap"),
  options
)).render();
