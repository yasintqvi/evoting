var colors = ["#727cf5", "#0acf97", "#fa5c7c"],
  dataColors = $("#basic-treemap").data("colors"),
  options = {
    series: [
      {
        data: [
          { x: "دهلی نو", y: 218 },
          { x: "کلکته", y: 149 },
          { x: "بمبئی", y: 184 },
          { x: "احمدآباد", y: 55 },
          { x: "بنگلورو", y: 84 },
          { x: "قیطار", y: 31 },
          { x: "چونای", y: 70 },
          { x: "جیبور", y: 30 },
          { x: "سوت", y: 44 },
          { x: "حیدرآباد", y: 68 },
          { x: "شادی", y: 28 },
          { x: "بی نظیر", y: 19 },
          { x: "کانپور", y: 29 },
        ],
      },
    ],
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    legend: { show: !1 },
    chart: { height: 350, type: "treemap" },
    title: { text: "Basic Treemap", align: "center" },
  },
  chart = new ApexCharts(document.querySelector("#basic-treemap"), options),
  colors = (chart.render(), ["#727cf5", "#0acf97", "#fa5c7c"]),
  dataColors = $("#multiple-treemap").data("colors"),
  options = {
    series: [
      {
        name: "دسک تاپ",
        data: [
          { x: "ا ب س", y: 10 },
          { x: "د ای اف", y: 60 },
          { x: "ایکس وای زد", y: 41 },
        ],
      },
      {
        name: "متحرک",
        data: [
          { x: "ا بی سی دی", y: 10 },
          { x: "یس اف حی", y: 20 },
          { x: "دبلیو ایکسس", y: 51 },
          { x: "پی کیو", y: 30 },
          { x: "ام ان او", y: 20 },
          { x: "سی دی او", y: 30 },
        ],
      },
    ],
    legend: { show: !1 },
    chart: { height: 350, type: "treemap" },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    title: { text: "نمودار چند بعدی", align: "center" },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#multiple-treemap"),
      options
    )).render(),
    ["#727cf5", "#0acf97", "#fa5c7c"]),
  dataColors = $("#distributed-treemap").data("colors"),
  options = {
    series: [
      {
        data: [
          { x: "دهلی نو", y: 218 },
          { x: "کلکته", y: 149 },
          { x: "بمبئی", y: 184 },
          { x: "احمدآباد", y: 55 },
          { x: "بنگلورو", y: 84 },
          { x: "قیطار", y: 31 },
          { x: "چونای", y: 70 },
          { x: "جیبور", y: 30 },
          { x: "سوت", y: 44 },
          { x: "حیدرآباد", y: 68 },
          { x: "شادی", y: 28 },
          { x: "بی نظیر", y: 19 },
          { x: "کانپور", y: 29 },
        ],
      },
    ],
    legend: { show: !1 },
    chart: { height: 350, type: "treemap" },
    title: {
      text: "تر مپ توزیع شده (رنگ متفاوت برای هر سلول)",
      align: "center",
    },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    plotOptions: { treemap: { distributed: !0, enableShades: !1 } },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#distributed-treemap"),
      options
    )).render(),
    ["#727cf5", "#0acf97", "#fa5c7c"]),
  dataColors = $("#color-range-treemap").data("colors"),
  options = {
    series: [
      {
        data: [
          { x: "قفل", y: 1.2 },
          { x: "بای", y: 0.4 },
          { x: "چمن", y: -1.4 },
          { x: "سعادت", y: 2.7 },
          { x: "گربه", y: -0.3 },
          { x: "سوها", y: 5.1 },
          { x: "خلق", y: -2.3 },
          { x: "جیانی", y: 2.1 },
          { x: "صفحه نمایش", y: 0.3 },
          { x: "بله", y: 0.12 },
          { x: "من", y: -2.31 },
          { x: "لغزنده", y: 3.98 },
          { x: "تبر", y: 1.67 },
        ],
      },
    ],
    legend: { show: !1 },
    chart: { height: 350, type: "treemap" },
    title: { text: "تری مپ با مقیاس رنگ", align: "center" },
    dataLabels: {
      enabled: !0,
      style: { fontSize: "12px" },
      formatter: function (e, a) {
        return [e, a.value];
      },
      offsetY: -4,
    },
    plotOptions: {
      treemap: {
        enableShades: !0,
        shadeIntensity: 0.5,
        reverseNegativeShade: !0,
        colorScale: {
          ranges: [
            {
              from: -6,
              to: 0,
              color: (colors = dataColors ? dataColors.split(",") : colors)[0],
            },
            { from: 0.001, to: 6, color: colors[1] },
          ],
        },
      },
    },
  };
(chart = new ApexCharts(
  document.querySelector("#color-range-treemap"),
  options
)).render();
