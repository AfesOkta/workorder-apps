// Saas Dashboard

// sparkline-chart-1
var options = {
  series: [
      {
          name: "Series A",
          data: [40, 36, 75, 10, 50],
      },
  ],
  chart: {
      height: 80,
      type: "line",
      sparkline: {
          enabled: true,
      },
      toolbar: {
          show: false,
      },
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      curve: "smooth",
      width: 2,
  },
  colors: ["#2a4fd7"],
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-1"), options);
chart.render();



// sparkline-chart-2
var options = {
  series: [
      {
          name: "Series A",
          data: [20, 24, 26, 32],
      },
  ],
  chart: {
      height: 80,
      type: "area",
      sparkline: {
          enabled: true,
      },
      toolbar: {
          show: false,
      },
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      curve: "smooth",
      width: 2,
  },
  fill: {
      colors: undefined,
      opacity: 0.2,
      type: "solid",
  },
  colors: ["#2a4fd7", "transparent"],
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-2"), options);
chart.render();



// sparkline-chart-3
var options = {
  series: [
      {
          name: "Series A",
          data: [40, 54, 70, 50, 60, 50, 60, 40, 50, 80, 90],
      },
  ],
  chart: {
      height: 80,
      type: "area",
      sparkline: {
          enabled: true,
      },
      toolbar: {
          show: false,
      },
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      curve: "straight",
      width: 2,
  },
  fill: {
      type: "gradient",
      gradient: {
          shadeIntensity: 1,
          inverseColors: false,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [50, 100, 100, 100],
      },
  },
  colors: ["#2a4fd7", "transparent"],
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-3"), options);
chart.render();



// sparkline-chart-4
var options = {
  series: [
      {
          name: "Series A",
          data: [30, 26, 40, 32],
      },
  ],
  chart: {
      height: 80,
      type: "area",
      sparkline: {
          enabled: true,
      },
      toolbar: {
          show: false,
      },
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      curve: "smooth",
      width: 2,
  },
  fill: {
      colors: undefined,
      opacity: 0.2,
      type: "solid",
  },
  colors: ["#2a4fd7", "transparent"],
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-4"), options);
chart.render();



//  column-chart-1
var options = {
  series: [
      {
          name: "Income",
          data: [6000, 4000, 3000, 4000, 5000, 7500, 6500, 4000, 5500, 7200, 8000, 9500],
      },
      {
          name: "Expenses",
          data: [4000, 3200, 2800, 3000, 4000, 5000, 5000, 2500, 4000, 6800, 6000, 7500],
      },
  ],
  chart: {
      type: "bar",
      height: 238,
      toolbar: {
          show: false,
      },
  },
  plotOptions: {
      bar: {
          horizontal: false,
          columnWidth: "25%",
          endingShape: "rounded",
      },
  },
  legend: {
      position: "top",
      horizontalAlign: "right",
      offsetX: 0,
      offsetY: 0,
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      show: true,
      width: 3,
      colors: ["transparent"],
  },
  xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
  },
  yaxis: {
      labels: {
          formatter: function (value) {
              return "$" + value;
          },
      },
  },
  fill: {
      opacity: 1,
  },
  colors: ["#5071ea", "#cad3f5"],
};

var chart = new ApexCharts(document.querySelector("#column-chart-1"), options);
chart.render();


// Pie Chart-1
var options = {
  series: [50, 40, 35, 20],
  chart: {
      type: "donut",
      height: 238,
  },

  plotOptions: {
      pie: {
          size: 100,
          donut: {
              size: "60%",
          },
      },
  },

  dataLabels: {
      enabled: false,
  },
  legend: {
      show: false,
  },

  colors: ["#2a4fd7", "#34c38f", "#f1b44c", "#35d1df"],
  responsive: [
      {
          breakpoint: 1025,
          options: {
              chart: {
                  height: 180,
              },
          },
      },
  ],
};

var chart = new ApexCharts(document.querySelector("#pie-chart-1"), options);
chart.render();



// pie-chart-2
var options = {
  series: [44, 50, 65],
  chart: {
      type: "donut",
      height: 250,
  },

  plotOptions: {
      pie: {
          expandOnClicks: true,
          hover: {
              pie: {
                  size: 200,
              },
          },
          donut: {
            labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: '22px',
                  fontFamily: 'Helvetica, Arial, sans-serif',
                  fontWeight: 600,
                  color: "#000",
                  offsetY: -10,
                  formatter: function (val) {
                    return val
                  }
                },
                value: {
                  show: true,
                  fontSize: '16px',
                  fontFamily: 'Helvetica, Arial, sans-serif',
                  fontWeight: 400,
                  color: "#000",
                  offsetY: 16,
                  formatter: function (val) {
                    return val
                  }
                },
            },
        },
    },
},

  dataLabels: {
      enabled: false,
  },
  legend: {
      show: false,
  },
  colors: ["#2a4fd7", "#f1b44c", "#f6f6f6"],
};

var chart = new ApexCharts(document.querySelector("#pie-chart-2"), options);
chart.render();



// column-chart-2
var options = {
    series: [{
    name: 'PRODUCT A',
    data: [44, 55, 41, 67, 22, 43, ]
  }, {
    name: 'PRODUCT B',
    data: [13, 23, 20, 8, 13, 27]
  }],
    chart: {
    type: 'bar',
    height: 250,
    stacked: true,
    toolbar: {
      show: false
    },
    zoom: {
      enabled: false
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "12%",
      endingShape: "rounded",
      
    },
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    categories: [],
    labels: {
        show: false,
    }
  },
  yaxis: {
    labels: {
        show: false,
    }
  },
  legend: {
          show: false,
      },
  fill: {
    opacity: 1
  },
    colors: ["#2a4fd7", "#f1b44c"],
  };

  var chart = new ApexCharts(document.querySelector("#column-chart-2"), options);
  chart.render();



// sparkline-chart-5
var options = {
  series: [
      {
          name: "Series A",
          data: [30, 50, 40, 60, 50, 70, 60, 80],
      },
      {
          name: "Series B",
          data: [30, 60, 50, 70, 60, 80, 70, 90],
      },
  ],
  chart: {
      height: 250,
      type: "area",
      sparkline: {
          enabled: true,
      },
      toolbar: {
          show: false,
      },
  },
  dataLabels: {
      enabled: false,
  },
  stroke: {
      curve: "smooth",
      width: 2,
  },
  fill: {
      type: "gradient",
      gradient: {
          shadeIntensity: 1,
          inverseColors: false,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [50, 100, 100, 100],
      },
  },
  colors: ["#2a4fd7", "#f1b44c"],
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-5"), options);
chart.render();



// column-chart-3
var options = {
  series: [
      {
          data: [60, 80, 100, 60, 80, 70],
      },
  ],
  chart: {
      type: "bar",
      height: 150,
      toolbar: {
          show: false,
      },
  },
  plotOptions: {
      bar: {
          horizontal: false,
          columnWidth: "24%",
          endingShape: "rounded",
          startingShape: "rounded",
      },
  },
  dataLabels: {
      enabled: false,
  },
  legend: {
      show: false,
  },
  stroke: {
      show: true,
      width: 3,
      colors: ["transparent"],
  },
  xaxis: {
      categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Set"],
      axisBorder: {
          show: false,
      },
      axisTicks: {
          show: false,
      },
  },
  fill: {
      opacity: 1,
  },
  tooltip: {
      y: {
          formatter: function (val) {
              return "$ " + val + " thousands";
          },
      },
  },
  yaxis: {
      axisBorder: {
          show: false,
      },
      axisTicks: {
          show: false,
      },
      labels: {
          show: false,
      },
  },
  colors: ["#34c38f"],
};

var chart = new ApexCharts(document.querySelector("#column-chart-3"), options);
chart.render();



// column-chart-4
var options = {
  series: [
      {
          data: [60, 80, 100, 60, 80, 70],
      },
  ],
  chart: {
      type: "bar",
      height: 150,
      toolbar: {
          show: false,
      },
  },
  plotOptions: {
      bar: {
          horizontal: false,
          columnWidth: "24%",
          endingShape: "rounded",
          startingShape: "rounded",
      },
  },
  dataLabels: {
      enabled: false,
  },
  legend: {
      show: false,
  },
  stroke: {
      show: true,
      width: 3,
      colors: ["transparent"],
  },
  xaxis: {
      categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Set"],
      axisBorder: {
          show: false,
      },
      axisTicks: {
          show: false,
      },
  },
  fill: {
      opacity: 1,
  },
  tooltip: {
      y: {
          formatter: function (val) {
              return "$ " + val + " thousands";
          },
      },
  },
  yaxis: {
      axisBorder: {
          show: false,
      },
      axisTicks: {
          show: false,
      },
      labels: {
          show: false,
      },
  },
  colors: ["#fa3452"],
};

var chart = new ApexCharts(document.querySelector("#column-chart-4"), options);
chart.render();
