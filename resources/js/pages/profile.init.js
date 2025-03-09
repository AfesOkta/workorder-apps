// Profile Dashboard


// sparkline-chart-1

var options7 = {
  series: [65],
  chart: {
  type: 'radialBar',
  height: 100,
  sparkline: {
    enabled: true
  }
},
dataLabels: {
  enabled: false
},
plotOptions: {
  radialBar: {
    hollow: {
      margin: 0,
      size: '60%'
    },
    track: {
      margin: 0
    },
    dataLabels: {
      show: false
    }
  }
},
colors:['#2a4fd7']
};

var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-1"), options7);
chart7.render();


  // sparkline-chart-2

  var options7 = {
    series: [30],
    chart: {
    type: 'radialBar',
    height: 100,
    sparkline: {
      enabled: true
    }
  },
  dataLabels: {
    enabled: false
  },
  plotOptions: {
    radialBar: {
      hollow: {
        margin: 0,
        size: '60%'
      },
      track: {
        margin: 0
      },
      dataLabels: {
        show: false
      }
    }
  },
  colors:['#2a4fd7']
  };

  var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-2"), options7);
  chart7.render();



// sparkline-chart-3

var options7 = {
  series: [78],
  chart: {
  type: 'radialBar',
  height: 100,
  sparkline: {
    enabled: true
  }
},
dataLabels: {
  enabled: false
},
plotOptions: {
  radialBar: {
    hollow: {
      margin: 0,
      size: '60%'
    },
    track: {
      margin: 0
    },
    dataLabels: {
      show: false
    }
  }
},
colors:['#2a4fd7']
};

var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-3"), options7);
chart7.render();

var options7 = {
    series: [78],
    chart: {
    type: 'radialBar',
    height: 100,
    sparkline: {
      enabled: true
    }
  },
  dataLabels: {
    enabled: false
  },
  plotOptions: {
    radialBar: {
      hollow: {
        margin: 0,
        size: '80%'
      },
      track: {
        margin: 0
      },
      dataLabels: {
        show: false
      }
    }
  },
  colors:['#2a4fd7']
  };

  var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-4"), options7);
  chart7.render();


// column-chart-1

var options = {
  series: [
      {
          name: "Sale Product",
          data: [20, 30, 20, 40, 30, 50, 40, 60, 50, 70, 60, 80],
      },
      {
          name: "Stock Product",
          data: [10, 15, 10, 20, 15, 25, 20, 30, 25, 35, 30, 40],
      },
  ],
  chart: {
      type: "bar",
      height: 312,
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
              return value + "k";
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

