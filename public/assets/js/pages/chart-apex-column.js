var colors = ["#727cf5", "#0acf97", "#fa5c7c"],
  dataColors = $("#basic-column").data("colors"),
  options = {
    chart: { height: 396, type: "bar", toolbar: { show: !1 } },
    plotOptions: {
      bar: { horizontal: !1, endingShape: "rounded", columnWidth: "55%" },
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ["transparent"] },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      { name: "سود خالص", data: [44, 55, 57, 56, 61, 58, 63, 60, 66] },
      { name: "درآمد", data: [76, 85, 101, 98, 87, 105, 91, 114, 94] },
      { name: "جریان نقدی آزاد", data: [35, 41, 36, 26, 45, 48, 52, 53, 41] },
    ],
    xaxis: {
      categories: [
        "اردیبهشت",
        "خرداد",
        "تیر",
        "مرداد",
        "شهریور",
        "مهر",
        "آبان",
        "آذر",
        "دی",
      ],
    },
    legend: { offsetY: 7 },
    yaxis: { title: { text: "تومان (هزار)" } },
    fill: { opacity: 1 },
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
      padding: { bottom: 5 },
    },
    tooltip: {
      y: {
        formatter: function (t) {
              return "تومان " + t + " هزار";
        },
      },
    },
  },
  chart = new ApexCharts(document.querySelector("#basic-column"), options),
  colors = (chart.render(), ["#727cf5"]),
  dataColors = $("#datalabels-column").data("colors"),
  options = {
    chart: { height: 380, type: "bar", toolbar: { show: !1 } },
    plotOptions: { bar: { borderRadius: 10, dataLabels: { position: "top" } } },
    dataLabels: {
      enabled: !0,
      formatter: function (t) {
        return t + "%";
      },
      offsetY: -25,
      style: { fontSize: "12px", colors: ["#304758"] },
    },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    legend: { show: !0, horizontalAlign: "center", offsetX: 0, offsetY: -5 },
    series: [
      {
        name: "تورم",
        data: [2.3, 3.1, 4, 10.1, 4, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2],
      },
    ],
    xaxis: {
      categories: [
        "فروردین",
        "اردیبهشت",
        "خرداد",
        "تیر",
        "مرداد",
        "شهریور",
        "مهر",
        "آبان",
        "آذر",
        "دی",
        "بهمن",
        "اسفند",
      ],
      position: "top",
      labels: { offsetY: 0 },
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      crosshairs: {
        fill: {
          type: "gradient",
          gradient: {
            colorFrom: "#D8E3F0",
            colorTo: "#BED1E6",
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5,
          },
        },
      },
      tooltip: { enabled: !0, offsetY: -10 },
    },
    fill: {
      gradient: {
        enabled: !1,
        shade: "light",
        type: "horizontal",
        shadeIntensity: 0.25,
        gradientToColors: void 0,
        inverseColors: !0,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [50, 0, 100, 100],
      },
    },
    yaxis: {
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      labels: {
        show: !1,
        formatter: function (t) {
          return t + "%";
        },
      },
    },
    title: {
      text: "تورم ماهانه در آرژانتین ، 1402",
      floating: !0,
      offsetY: 360,
      align: "center",
      style: { color: "#444" },
    },
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#datalabels-column"),
      options
    )).render(),
    ["#39afd1", "#ffbc00", "#e3eaef"]),
  dataColors = $("#stacked-column").data("colors"),
  options = {
    chart: { height: 380, type: "bar", stacked: !0, toolbar: { show: !1 } },
    plotOptions: { bar: { horizontal: !1, columnWidth: "50%" } },
    series: [
      { name: "محصول آ", data: [44, 55, 41, 67, 22, 43, 21, 49] },
      { name: "محصول ب", data: [13, 23, 20, 8, 13, 27, 33, 12] },
      { name: "محصول س", data: [11, 17, 15, 15, 21, 14, 15, 13] },
    ],
    xaxis: {
      categories: [
        "1401  ب 1",
        "1401  ب 2",
        "1401  ب 3",
        "1401  ب 4",
        "1402  ب 1",
        "1402  ب 2",
        "1402  ب 3",
        "1402  ب 4",
      ],
    },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    fill: { opacity: 1 },
    legend: { offsetY: 7 },
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
      padding: { bottom: 5 },
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#stacked-column"),
      options
    )).render(),
    ["#39afd1", "#0acf97", "#e3eaef"]),
  dataColors = $("#full-stacked-column").data("colors"),
  options = {
    chart: {
      height: 380,
      type: "bar",
      stacked: !0,
      stackType: "100%",
      toolbar: { show: !1 },
    },
    plotOptions: { bar: { columnWidth: "50%" } },
    series: [
      { name: "محصول آ", data: [44, 55, 41, 67, 22, 43, 21, 49] },
      { name: "محصول ب", data: [13, 23, 20, 8, 13, 27, 33, 12] },
      { name: "محصول س", data: [11, 17, 15, 15, 21, 14, 15, 13] },
    ],
    xaxis: {
      categories: [
        "1401  ب 1",
        "1401  ب 2",
        "1401  ب 3",
        "1401  ب 4",
        "1402  ب 1",
        "1402  ب 2",
        "1402  ب 3",
        "1402  ب 4",
      ],
    },
    fill: { opacity: 1 },
    legend: { offsetY: 7 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
      padding: { bottom: 5 },
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#full-stacked-column"),
      options
    )).render(),
    ["#0acf97", "#fa5c7c"]),
  dataColors = $("#column-with-markers").data("colors"),
  options = {
    series: [
      {
        name: "واقعی",
        data: [
          {
            x: "1391",
            y: 1292,
            goals: [
              {
                name: "مورد انتظار",
                value: 1400,
                strokeHeight: 5,
                strokeColor: (colors = dataColors
                  ? dataColors.split(",")
                  : colors)[1],
              },
            ],
          },
          {
            x: "1392",
            y: 4432,
            goals: [
              {
                name: "مورد انتظار",
                value: 5400,
                strokeHeight: 5,
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1393",
            y: 5423,
            goals: [
              {
                name: "مورد انتظار",
                value: 5200,
                strokeHeight: 5,
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1394",
            y: 6653,
            goals: [
              {
                name: "مورد انتظار",
                value: 6500,
                strokeHeight: 5,
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1395",
            y: 8133,
            goals: [
              {
                name: "مورد انتظار",
                value: 6600,
                strokeHeight: 13,
                strokeWidth: 0,
                strokeLineCap: "round",
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1396",
            y: 7132,
            goals: [
              {
                name: "مورد انتظار",
                value: 7500,
                strokeHeight: 5,
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1397",
            y: 7332,
            goals: [
              {
                name: "مورد انتظار",
                value: 8700,
                strokeHeight: 5,
                strokeColor: colors[1],
              },
            ],
          },
          {
            x: "1398",
            y: 6553,
            goals: [
              {
                name: "مورد انتظار",
                value: 7300,
                strokeHeight: 2,
                strokeDashArray: 2,
                strokeColor: colors[1],
              },
            ],
          },
        ],
      },
    ],
    chart: { height: 380, type: "bar" },
    plotOptions: { bar: { columnWidth: "60%" } },
    colors: colors,
    dataLabels: { enabled: !1 },
    legend: {
      show: !0,
      showForSingleSeries: !0,
      customLegendItems: ["واقعی", "مورد انتظار"],
      markers: { fillColors: colors },
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#column-with-markers"),
      options
    )).render(),
    ["#0acf97", "#fa5c7c"]),
  optionsGroup =
    ((dataColors = $("#column-with-group-label").data("colors")) &&
      (colors = dataColors.split(",")),
    dayjs.extend(window.dayjs_plugin_quarterOfYear),
    {
      series: [
        {
          name: "فروش",
          data: [
            { x: "2020/01/01", y: 400 },
            { x: "2020/04/01", y: 430 },
            { x: "2020/07/01", y: 448 },
            { x: "2020/10/01", y: 470 },
            { x: "2021/01/01", y: 540 },
            { x: "2021/04/01", y: 580 },
            { x: "2021/07/01", y: 690 },
            { x: "2021/10/01", y: 690 },
          ],
        },
      ],
      chart: { type: "bar", height: 380, toolbar: { show: !1 } },
      plotOptions: { bar: { horizontal: !1, columnWidth: "45%" } },
      colors: colors,
      xaxis: {
        type: "category",
        labels: {
          formatter: function (t) {
            return " ب " + dayjs(t).quarter();
          },
        },
        group: {
          style: { fontSize: "10px", fontWeight: 700 },
          groups: [
            { title: "2020", cols: 4 },
            { title: "2021", cols: 4 },
          ],
        },
      },
      tooltip: {
        x: {
          formatter: function (t) {
            return " ب " + dayjs(t).quarter() + " " + dayjs(t).format("YYYY");
          },
        },
      },
    }),
  chartGroup = new ApexCharts(
    document.querySelector("#column-with-group-label"),
    optionsGroup
  ),
  colors = (chartGroup.render(), ["#fa5c7c"]),
  dataColors = $("#rotate-labels-column").data("colors"),
  options = {
    annotations: {
      points: [
        {
          x: "موزهای",
          seriesIndex: 0,
          label: {
            borderColor: "#727cf5",
            offsetY: 0,
            style: { color: "#fff", background: "#727cf5" },
            text: "موز خوب است",
          },
        },
      ],
    },
    chart: { height: 380, type: "bar", toolbar: { show: !1 } },
    plotOptions: { bar: { columnWidth: "50%", endingShape: "rounded" } },
    dataLabels: { enabled: !1 },
    stroke: { width: 2 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      {
        name: "وعده",
        data: [44, 55, 41, 67, 22, 43, 21, 33, 45, 31, 87, 65, 35],
      },
    ],
    grid: {
      borderColor: "#f1f3fa",
      padding: { top: 0, right: -2, bottom: -35, left: 10 },
    },
    xaxis: {
      labels: { rotate: -45 },
      categories: [
        "سیب",
        "پرتقال",
        "توت فرنگی",
        "آناناس",
        "انبه",
        "موزهای",
        "توت سیاه",
        "گلابی",
        "هندوانه",
        "گیلاس",
        "انار",
        "نارنجی",
        "پاپیا",
      ],
    },
    yaxis: { title: { text: "وعده" } },
    fill: {
      type: "gradient",
      gradient: {
        shade: "light",
        type: "horizontal",
        shadeIntensity: 0.25,
        gradientToColors: void 0,
        inverseColors: !0,
        opacityFrom: 0.85,
        opacityTo: 0.85,
        stops: [50, 0, 100],
      },
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#rotate-labels-column"),
      options
    )).render(),
    ["#727cf5"]),
  dataColors = $("#negative-value-column").data("colors"),
  options = {
    chart: { height: 380, type: "bar", toolbar: { show: !1 } },
    plotOptions: {
      bar: {
        colors: {
          ranges: [
            { from: -100, to: -46, color: "#fa5c7c" },
            { from: -45, to: 0, color: "#ffbc00" },
          ],
        },
        columnWidth: "80%",
      },
    },
    dataLabels: { enabled: !1 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      {
        name: "جریان نقدی",
        data: [
          1.45, 5.42, 5.9, -0.42, -12.6, -18.1, -18.2, -14.16, -11.1, -6.09,
          0.34, 3.88, 13.07, 5.8, 2, 7.37, 8.1, 13.57, 15.75, 17.1, 19.8,
          -27.03, -54.4, -47.2, -43.3, -18.6, -48.6, -41.1, -39.6, -37.6, -29.4,
          -21.4, -2.4,
        ],
      },
    ],
    yaxis: {
      title: { text: "رشد" },
      labels: {
        formatter: function (t) {
          return t.toFixed(0) + "%";
        },
      },
    },
    xaxis: {
      categories: [
        "2011-01-01",
        "2011-02-01",
        "2011-03-01",
        "2011-04-01",
        "2011-05-01",
        "2011-06-01",
        "2011-07-01",
        "2011-08-01",
        "2011-09-01",
        "2011-10-01",
        "2011-11-01",
        "2011-12-01",
        "2012-01-01",
        "2012-02-01",
        "2012-03-01",
        "2012-04-01",
        "2012-05-01",
        "2012-06-01",
        "2012-07-01",
        "2012-08-01",
        "2012-09-01",
        "2012-10-01",
        "2012-11-01",
        "2012-12-01",
        "2013-01-01",
        "2013-02-01",
        "2013-03-01",
        "2013-04-01",
        "2013-05-01",
        "2013-06-01",
        "2013-07-01",
        "2013-08-01",
        "2013-09-01",
      ],
      labels: { rotate: -90 },
    },
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#negative-value-column"),
      options
    )).render(),
    [
      "#727cf5",
      "#6c757d",
      "#0acf97",
      "#fa5c7c",
      "#ffbc00",
      "#39afd1",
      "#e3eaef",
      "#313a46",
    ]),
  dataColors = $("#distributed-column").data("colors"),
  options = {
    chart: {
      height: 380,
      type: "bar",
      toolbar: { show: !1 },
      events: {
        click: function (t, o, a) {
          console.log(t, o, a);
        },
      },
    },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    plotOptions: { bar: { columnWidth: "45%", distributed: !0 } },
    dataLabels: { enabled: !1 },
    series: [{ data: [21, 22, 10, 28, 16, 21, 13, 30] }],
    xaxis: {
      categories: [
        "جان",
        "جو",
        "جیک",
        "عنبر",
        "پیتر",
        "مریم",
        "داود",
        "سوسن",
      ],
      labels: { style: { colors: colors, fontSize: "14px" } },
    },
    legend: { offsetY: 7 },
    grid: {
      row: { colors: ["transparent", "transparent"], opacity: 0.2 },
      borderColor: "#f1f3fa",
    },
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#distributed-column"),
      options
    )).render(),
    ["#0acf97", "#39afd1"]),
  dataColors = $("#range-column").data("colors"),
  options = {
    chart: { height: 380, type: "rangeBar" },
    plotOptions: { bar: { horizontal: !1 } },
    dataLabels: { enabled: !0 },
    legend: { offsetY: 7 },
    colors: (colors = dataColors ? dataColors.split(",") : colors),
    series: [
      {
        name: "محصول آ",
        data: [
          { x: "تیم آ", y: [1, 5] },
          { x: "تیم ب", y: [4, 6] },
          { x: "تیم س", y: [5, 8] },
          { x: "تیم د", y: [3, 11] },
        ],
      },
      {
        name: "محصول ب",
        data: [
          { x: "تیم آ", y: [2, 6] },
          { x: "تیم ب", y: [1, 3] },
          { x: "تیم س", y: [7, 8] },
          { x: "تیم د", y: [5, 9] },
        ],
      },
    ],
  },
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#range-column"),
      options
    )).render(),
    [
      "#727cf5",
      "#6c757d",
      "#0acf97",
      "#fa5c7c",
      "#ffbc00",
      "#39afd1",
      "#e3eaef",
      "#313a46",
    ]);
function shuffleArray(t) {
  for (var o = t.length - 1; 0 < o; o--) {
    var a = Math.floor(Math.random() * (o + 1)),
      e = t[o];
    (t[o] = t[a]), (t[a] = e);
  }
  return t;
}
(dataColors = $("#chart-year").data("colors")) &&
  (colors = dataColors.split(",")),
  (Apex = {
    chart: { toolbar: { show: !1 } },
    tooltip: { shared: !1 },
    legend: { show: !1 },
  });
var arrayData = [
  {
    y: 400,
    quarters: [
      { x: " ب 1", y: 120 },
      { x: " ب 2", y: 90 },
      { x: " ب 3", y: 100 },
      { x: " ب 4", y: 90 },
    ],
  },
  {
    y: 430,
    quarters: [
      { x: " ب 1", y: 120 },
      { x: " ب 2", y: 110 },
      { x: " ب 3", y: 90 },
      { x: " ب 4", y: 110 },
    ],
  },
  {
    y: 448,
    quarters: [
      { x: " ب 1", y: 70 },
      { x: " ب 2", y: 100 },
      { x: " ب 3", y: 140 },
      { x: " ب 4", y: 138 },
    ],
  },
  {
    y: 470,
    quarters: [
      { x: " ب 1", y: 150 },
      { x: " ب 2", y: 60 },
      { x: " ب 3", y: 190 },
      { x: " ب 4", y: 70 },
    ],
  },
  {
    y: 540,
    quarters: [
      { x: " ب 1", y: 120 },
      { x: " ب 2", y: 120 },
      { x: " ب 3", y: 130 },
      { x: " ب 4", y: 170 },
    ],
  },
  {
    y: 580,
    quarters: [
      { x: " ب 1", y: 170 },
      { x: " ب 2", y: 130 },
      { x: " ب 3", y: 120 },
      { x: " ب 4", y: 160 },
    ],
  },
];
function makeData() {
  var t = shuffleArray(arrayData);
  return [
    { x: "1391", y: t[0].y, color: colors[0], quarters: t[0].quarters },
    { x: "1392", y: t[1].y, color: colors[1], quarters: t[1].quarters },
    { x: "1393", y: t[2].y, color: colors[2], quarters: t[2].quarters },
    { x: "1394", y: t[3].y, color: colors[3], quarters: t[3].quarters },
    { x: "1395", y: t[4].y, color: colors[4], quarters: t[4].quarters },
    { x: "1396", y: t[5].y, color: colors[5], quarters: t[5].quarters },
  ];
}
function updateQuarterChart(t, o) {
  var a = [],
    e = [];
  if (t.w.globals.selectedDataPoints[0]) {
    for (var r = t.w.globals.selectedDataPoints, s = 0; s < r[0].length; s++) {
      var l = r[0][s],
        n = t.w.config.series[0];
      a.push({ name: n.data[l].x, data: n.data[l].quarters }),
        e.push(n.data[l].color);
    }
    return (
      0 === a.length && (a = [{ data: [] }]),
      ApexCharts.exec(o, "updateOptions", {
        series: a,
        colors: e,
        fill: { colors: e },
      })
    );
  }
}
var options = {
    series: [{ data: makeData() }],
    chart: {
      id: "barYear",
      height: 400,
      width: "100%",
      type: "bar",
      events: {
        dataPointSelection: function (t, o, a) {
          var e = document.querySelector("#chart-quarter"),
            r = document.querySelector("#chart-year");
          1 !== a.selectedDataPoints[0].length ||
            e.classList.contains("active") ||
            (r.classList.add("chart-quarter-activated"),
            e.classList.add("active")),
            updateQuarterChart(o, "barQuarter"),
            0 === a.selectedDataPoints[0].length &&
              (r.classList.remove("chart-quarter-activated"),
              e.classList.remove("active"));
        },
        updated: function (t) {
          updateQuarterChart(t, "barQuarter");
        },
      },
    },
    plotOptions: {
      bar: {
        distributed: !0,
        horizontal: !0,
        barHeight: "75%",
        dataLabels: { position: "bottom" },
      },
    },
    dataLabels: {
      enabled: !0,
      textAnchor: "start",
      style: { colors: ["#fff"] },
      formatter: function (t, o) {
        return o.w.globals.labels[o.dataPointIndex];
      },
      offsetX: 0,
      dropShadow: { enabled: !0 },
    },
    colors: colors,
    states: {
      normal: { filter: { type: "desaturate" } },
      active: {
        allowMultipleDataPointsSelection: !0,
        filter: { type: "darken", value: 1 },
      },
    },
    tooltip: {
      x: { show: !1 },
      y: {
        title: {
          formatter: function (t, o) {
            return o.w.globals.labels[o.dataPointIndex];
          },
        },
      },
    },
    title: { text: "نتایج سالانه", offsetX: 15 },
    subtitle: { text: "(برای دیدن جزئیات روی نوار کلیک کنید)", offsetX: 15 },
    xaxis: { axisBorder: { show: !1 } },
    yaxis: { labels: { show: !1 } },
  },
  optionsQuarter =
    ((chart = new ApexCharts(
      document.querySelector("#chart-year"),
      options
    )).render(),
    {
      series: [{ data: [] }],
      chart: {
        id: "barQuarter",
        height: 400,
        width: "100%",
        type: "bar",
        stacked: !0,
      },
      plotOptions: { bar: { columnWidth: "50%", horizontal: !1 } },
      legend: { show: !1 },
      grid: { yaxis: { lines: { show: !1 } }, xaxis: { lines: { show: !0 } } },
      xaxis: { axisBorder: { show: !1 } },
      yaxis: { labels: { show: !1 } },
      title: { text: " نتایج نهایی", offsetX: 10 },
      tooltip: {
        x: {
          formatter: function (t, o) {
            return o.w.globals.seriesNames[o.seriesIndex];
          },
        },
        y: {
          title: {
            formatter: function (t, o) {
              return o.w.globals.labels[o.dataPointIndex];
            },
          },
        },
      },
    }),
  chartQuarter = new ApexCharts(
    document.querySelector("#chart-quarter"),
    optionsQuarter
  );
chartQuarter.render(),
  chart.addEventListener("dataPointSelection", function (t, o, a) {
    var e = document.querySelector("#chart-quarter"),
      r = document.querySelector("#chart-year");
    1 !== a.selectedDataPoints[0].length ||
      e.classList.contains("active") ||
      (r.classList.add("chart-quarter-activated"), e.classList.add("active")),
      updateQuarterChart(o, "barQuarter"),
      0 === a.selectedDataPoints[0].length &&
        (r.classList.remove("chart-quarter-activated"),
        e.classList.remove("active"));
  }),
  chart.addEventListener("updated", function (t) {
    updateQuarterChart(t, "barQuarter");
  }),
  document.querySelector("#model").addEventListener("change", function (t) {
    chart.updateSeries([{ data: makeData() }]);
  });
