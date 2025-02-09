var dataColors = $("#basic-timeline").data("colors"),
  options =
    (dataColors && (colors = dataColors.split(",")),
    {
      series: [
        {
          data: [
            {
              x: "کد",
              y: [
                new Date("2019-03-02").getTime(),
                new Date("2019-03-04").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-04").getTime(),
                new Date("2019-03-08").getTime(),
              ],
            },
            {
              x: "اعتبارسنجی",
              y: [
                new Date("2019-03-08").getTime(),
                new Date("2019-03-12").getTime(),
              ],
            },
            {
              x: "دیپلوی",
              y: [
                new Date("2019-03-12").getTime(),
                new Date("2019-03-18").getTime(),
              ],
            },
          ],
        },
      ],
      chart: { height: 350, type: "rangeBar", toolbar: { show: !1 } },
      colors: colors,
      plotOptions: { bar: { horizontal: !0 } },
      xaxis: { type: "datetime", axisBorder: { show: !1 } },
    }),
  chart = new ApexCharts(document.querySelector("#basic-timeline"), options),
  colors =
    (chart.render(), ["#727cf5", "#0acf97", "#fa5c7c", "#6c757d", "#39afd1"]),
  options =
    ((dataColors = $("#distributed-timeline").data("colors")) &&
      (colors = dataColors.split(",")),
    {
      series: [
        {
          data: [
            {
              x: "تجزیه",
              y: [
                new Date("2019-02-27").getTime(),
                new Date("2019-03-04").getTime(),
              ],
              fillColor: colors[0],
            },
            {
              x: "طراحی",
              y: [
                new Date("2019-03-04").getTime(),
                new Date("2019-03-08").getTime(),
              ],
              fillColor: colors[1],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-07").getTime(),
                new Date("2019-03-10").getTime(),
              ],
              fillColor: colors[2],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-08").getTime(),
                new Date("2019-03-12").getTime(),
              ],
              fillColor: colors[3],
            },
            {
              x: "دیپلوی",
              y: [
                new Date("2019-03-12").getTime(),
                new Date("2019-03-17").getTime(),
              ],
              fillColor: colors[4],
            },
          ],
        },
      ],
      chart: { height: 350, type: "rangeBar", toolbar: { show: !1 } },
      plotOptions: {
        bar: {
          horizontal: !0,
          distributed: !0,
          dataLabels: { hideOverflowingLabels: !1 },
        },
      },
      dataLabels: {
        enabled: !0,
        formatter: function (e, t) {
          var t = t.w.globals.labels[t.dataPointIndex],
            a = moment(e[0]),
            e = moment(e[1]).diff(a, "روز");
          return t + ": " + e + (1 < e ? " روز" : " روز");
        },
        style: { colors: ["#f3f4f5", "#fff"] },
      },
      xaxis: { type: "datetime", axisBorder: { show: !1 } },
      yaxis: { show: !0 },
    }),
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#distributed-timeline"),
      options
    )).render(),
    ["#727cf5", "#0acf97", "#fa5c7c", "#6c757d", "#39afd1"]),
  options =
    ((dataColors = $("#multi-series-timeline").data("colors")) &&
      (colors = dataColors.split(",")),
    {
      series: [
        {
          name: "گندم",
          data: [
            {
              x: "طراحی",
              y: [
                new Date("2019-03-05").getTime(),
                new Date("2019-03-08").getTime(),
              ],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-08").getTime(),
                new Date("2019-03-11").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-11").getTime(),
                new Date("2019-03-16").getTime(),
              ],
            },
          ],
        },
        {
          name: "جو",
          data: [
            {
              x: "طراحی",
              y: [
                new Date("2019-03-02").getTime(),
                new Date("2019-03-05").getTime(),
              ],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-06").getTime(),
                new Date("2019-03-09").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-10").getTime(),
                new Date("2019-03-19").getTime(),
              ],
            },
          ],
        },
      ],
      chart: { height: 350, type: "rangeBar", toolbar: { show: !1 } },
      plotOptions: { bar: { horizontal: !0 } },
      dataLabels: {
        enabled: !0,
        formatter: function (e) {
          var t = moment(e[0]),
            e = moment(e[1]).diff(t, "روز");
          return e + (1 < e ? " روز" : " روز");
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          shade: "light",
          type: "vertical",
          shadeIntensity: 0.25,
          gradientToColors: void 0,
          inverseColors: !0,
          opacityFrom: 1,
          opacityTo: 1,
          stops: [50, 0, 100, 100],
        },
      },
      colors: colors,
      xaxis: { type: "datetime", axisBorder: { show: !1 } },
      legend: { position: "top" },
    }),
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#multi-series-timeline"),
      options
    )).render(),
    ["#727cf5", "#0acf97", "#fa5c7c", "#6c757d", "#39afd1"]),
  options =
    ((dataColors = $("#advanced-timeline").data("colors")) &&
      (colors = dataColors.split(",")),
    {
      series: [
        {
          name: "گندم",
          data: [
            {
              x: "طراحی",
              y: [
                new Date("2019-03-05").getTime(),
                new Date("2019-03-08").getTime(),
              ],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-02").getTime(),
                new Date("2019-03-05").getTime(),
              ],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-05").getTime(),
                new Date("2019-03-07").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-03").getTime(),
                new Date("2019-03-09").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-08").getTime(),
                new Date("2019-03-11").getTime(),
              ],
            },
            {
              x: "اعتبارسنجی",
              y: [
                new Date("2019-03-11").getTime(),
                new Date("2019-03-16").getTime(),
              ],
            },
            {
              x: "طراحی",
              y: [
                new Date("2019-03-01").getTime(),
                new Date("2019-03-03").getTime(),
              ],
            },
          ],
        },
        {
          name: "جو",
          data: [
            {
              x: "طراحی",
              y: [
                new Date("2019-03-02").getTime(),
                new Date("2019-03-05").getTime(),
              ],
            },
            {
              x: "تست",
              y: [
                new Date("2019-03-06").getTime(),
                new Date("2019-03-16").getTime(),
              ],
              goals: [
                {
                  name: "شکستن",
                  value: new Date("2019-03-10").getTime(),
                  strokeColor: "#CD2F2A",
                },
              ],
            },
            {
              x: "کد",
              y: [
                new Date("2019-03-03").getTime(),
                new Date("2019-03-07").getTime(),
              ],
            },
            {
              x: "دیپلوی",
              y: [
                new Date("2019-03-20").getTime(),
                new Date("2019-03-22").getTime(),
              ],
            },
            {
              x: "طراحی",
              y: [
                new Date("2019-03-10").getTime(),
                new Date("2019-03-16").getTime(),
              ],
            },
          ],
        },
        {
          name: "وت",
          data: [
            {
              x: "کد",
              y: [
                new Date("2019-03-10").getTime(),
                new Date("2019-03-17").getTime(),
              ],
            },
            {
              x: "اعتبارسنجی",
              y: [
                new Date("2019-03-05").getTime(),
                new Date("2019-03-09").getTime(),
              ],
              goals: [
                {
                  name: "شکستن",
                  value: new Date("2019-03-07").getTime(),
                  strokeColor: "#CD2F2A",
                },
              ],
            },
          ],
        },
      ],
      chart: { height: 350, type: "rangeBar", toolbar: { show: !1 } },
      plotOptions: { bar: { horizontal: !0, barHeight: "80%" } },
      xaxis: { type: "datetime", axisBorder: { show: !1 } },
      stroke: { width: 1 },
      colors: colors,
      fill: { type: "solid", opacity: 0.6 },
      legend: { position: "top", horizontalAlign: "left" },
    }),
  colors =
    ((chart = new ApexCharts(
      document.querySelector("#advanced-timeline"),
      options
    )).render(),
    ["#727cf5", "#0acf97", "#fa5c7c", "#6c757d", "#39afd1"]),
  options =
    ((dataColors = $("#group-rows-timeline").data("colors")) &&
      (colors = dataColors.split(",")),
    {
      series: [
        {
          name: "جورج واشنگتن",
          data: [
            {
              x: "رئیس جمهور",
              y: [
                new Date(1789, 3, 30).getTime(),
                new Date(1797, 2, 4).getTime(),
              ],
            },
          ],
        },
        {
          name: "جان آدامز",
          data: [
            {
              x: "رئیس جمهور",
              y: [
                new Date(1797, 2, 4).getTime(),
                new Date(1801, 2, 4).getTime(),
              ],
            },
            {
              x: " رئیس جمهور",
              y: [
                new Date(1789, 3, 21).getTime(),
                new Date(1797, 2, 4).getTime(),
              ],
            },
          ],
        },
        {
          name: "توماس جفرسون",
          data: [
            {
              x: "رئیس جمهور",
              y: [
                new Date(1801, 2, 4).getTime(),
                new Date(1809, 2, 4).getTime(),
              ],
            },
            {
              x: " رئیس جمهور",
              y: [
                new Date(1797, 2, 4).getTime(),
                new Date(1801, 2, 4).getTime(),
              ],
            },
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1790, 2, 22).getTime(),
                new Date(1793, 11, 31).getTime(),
              ],
            },
          ],
        },
        {
          name: "هارون بور",
          data: [
            {
              x: " رئیس جمهور",
              y: [
                new Date(1801, 2, 4).getTime(),
                new Date(1805, 2, 4).getTime(),
              ],
            },
          ],
        },
        {
          name: "جورج کلینتون",
          data: [
            {
              x: " رئیس جمهور",
              y: [
                new Date(1805, 2, 4).getTime(),
                new Date(1812, 3, 20).getTime(),
              ],
            },
          ],
        },
        {
          name: "جان جی",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1789, 8, 25).getTime(),
                new Date(1790, 2, 22).getTime(),
              ],
            },
          ],
        },
        {
          name: "ادموند راندولف",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1794, 0, 2).getTime(),
                new Date(1795, 7, 20).getTime(),
              ],
            },
          ],
        },
        {
          name: "تیموتی پیکرینگ",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1795, 7, 20).getTime(),
                new Date(1800, 4, 12).getTime(),
              ],
            },
          ],
        },
        {
          name: "چارلز لی",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1800, 4, 13).getTime(),
                new Date(1800, 5, 5).getTime(),
              ],
            },
          ],
        },
        {
          name: "جان مارشال",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1800, 5, 13).getTime(),
                new Date(1801, 2, 4).getTime(),
              ],
            },
          ],
        },
        {
          name: "لوی لینکلن",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1801, 2, 5).getTime(),
                new Date(1801, 4, 1).getTime(),
              ],
            },
          ],
        },
        {
          name: "جیمز مدیسون",
          data: [
            {
              x: "وزیر امور خارجه",
              y: [
                new Date(1801, 4, 2).getTime(),
                new Date(1809, 2, 3).getTime(),
              ],
            },
          ],
        },
      ],
      chart: { height: 350, type: "rangeBar", toolbar: { show: !1 } },
      plotOptions: {
        bar: { horizontal: !0, barHeight: "50%", rangeBarGroupRows: !0 },
      },
      colors: colors,
      fill: { type: "solid" },
      xaxis: { type: "datetime", axisBorder: { show: !1 } },
      legend: { position: "right" },
    });
(chart = new ApexCharts(
  document.querySelector("#group-rows-timeline"),
  options
)).render();
