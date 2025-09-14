// درآمد / هزینه / سرمایه گذاری / پس انداز
(function () {
    const elRevenue = document.querySelector("#revenue-chart");
    if (elRevenue) {
        let colors = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"];
        const dataColors = elRevenue.getAttribute("data-colors");

        let optionsRevenue = {
            series: [
                {
                    name: "کل درآمد",
                    type: "bar",
                    data: [
                        89.25, 98.58, 68.74, 108.87, 77.54, 84.03, 51.24, 28.57,
                        92.57, 42.36, 88.51, 36.57,
                    ],
                },
                {
                    name: "کل هزینه ها",
                    type: "bar",
                    data: [
                        22.25, 24.58, 36.74, 22.87, 19.54, 25.03, 29.24, 10.57,
                        24.57, 35.36, 20.51, 17.57,
                    ],
                },
                {
                    name: "سرمایه گذاری",
                    type: "area",
                    data: [34, 65, 46, 68, 49, 61, 42, 44, 78, 52, 63, 67],
                },
                {
                    name: "پس انداز",
                    type: "line",
                    data: [8, 12, 7, 17, 21, 11, 5, 9, 7, 29, 12, 35],
                },
            ],
            chart: { height: 300, type: "line", toolbar: { show: false } },
            stroke: {
                dashArray: [0, 0, 0, 8],
                width: [0, 0, 2, 2],
                curve: "smooth",
            },
            fill: {
                opacity: [1, 1, 0.1, 1],
                type: ["gradient", "solid", "solid", "solid"],
                gradient: {
                    type: "vertical",
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 70],
                },
            },
            markers: { size: [0, 0, 0, 0], strokeWidth: 2, hover: { size: 4 } },
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
            },
            yaxis: { labels: { formatter: (val) => val + "هزار" } },
            grid: { padding: { top: 0, right: -15, bottom: 15, left: -15 } },
            legend: { horizontalAlign: "center" },
            plotOptions: { bar: { columnWidth: "50%", borderRadius: 3 } },
            colors: dataColors ? dataColors.split(",") : colors,
        };

        new ApexCharts(elRevenue, optionsRevenue).render();
    }
})();

// رادیال بار
(function () {
    const elRadial = document.querySelector("#multiple-radialbar");
    if (elRadial) {
        let colors = ["#6C757D", "#FFBC00", "#727CF5", "#0ACF97"];
        const dataColors = elRadial.getAttribute("data-colors");

        let optionsRadial = {
            chart: { height: 330, type: "radialBar" },
            plotOptions: {
                radialBar: {
                    track: {
                        margin: "17%",
                        background: "rgba(170,184,197, 0.2)",
                    },
                    hollow: { size: "1%" },
                    dataLabels: {
                        name: { show: false },
                        value: { show: false },
                    },
                },
            },
            stroke: { lineCap: "round" },
            colors: dataColors ? dataColors.split(",") : colors,
            series: [44, 55, 67, 22],
            labels: ["کامل", "در حال انجام", "هنوز در شروع", "لغو شده"],
            responsive: [
                { breakpoint: 380, options: { chart: { height: 260 } } },
            ],
        };

        new ApexCharts(elRadial, optionsRadial).render();
    }
})();
