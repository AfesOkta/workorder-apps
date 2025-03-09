/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/dashboard.init.js":
/*!**********************************************!*\
  !*** ./resources/js/pages/dashboard.init.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
Template Name: Drezon - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Version: 1.0.0
Website: https://themesbrand.com/
Contact: support@themesbrand.com
File: Dashboard
*/
//
// Total Revenue Chart
//
var options5 = {
  series: [{
    data: [10, 20, 15, 40, 20, 50, 70, 60, 90, 70, 110]
  }],
  chart: {
    type: 'bar',
    height: 50,
    sparkline: {
      enabled: true
    }
  },
  plotOptions: {
    bar: {
      columnWidth: '50%'
    }
  },
  tooltip: {
    fixed: {
      enabled: false
    },
    y: {
      title: {
        formatter: function formatter(seriesName) {
          return '';
        }
      }
    }
  },
  colors: ['#2a4fd7']
};
var chart5 = new ApexCharts(document.querySelector("#sparkline-chart-1"), options5);
chart5.render(); // sparkline-chart-2

var options = {
  series: [{
    name: "Series A",
    data: [10, 90, 30, 60, 50, 90, 25, 55, 30, 40]
  }],
  chart: {
    height: 50,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-2"), options);
chart.render(); // sparkline-chart-3

var options5 = {
  series: [{
    data: [40, 20, 30, 40, 20, 60, 55, 70, 95, 65, 110]
  }],
  chart: {
    type: 'bar',
    height: 50,
    sparkline: {
      enabled: true
    }
  },
  plotOptions: {
    bar: {
      columnWidth: '50%'
    }
  },
  tooltip: {
    fixed: {
      enabled: false
    },
    y: {
      title: {
        formatter: function formatter(seriesName) {
          return '';
        }
      }
    }
  },
  colors: ['#2a4fd7']
};
var chart5 = new ApexCharts(document.querySelector("#sparkline-chart-3"), options5);
chart5.render(); // sparkline-chart-4

var options = {
  series: [{
    name: "Series A",
    data: [10, 90, 30, 60, 50, 90, 25, 55, 30, 40]
  }],
  chart: {
    height: 50,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-4"), options);
chart.render(); // sparkline-chart-5

var options = {
  series: [{
    name: "Series A",
    data: [40, 40, 60, 30, 50, 40]
  }],
  chart: {
    height: 100,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-5"), options);
chart.render(); // sparkline-chart-6

var options = {
  series: [{
    name: "Series A",
    data: [48, 55, 40, 60, 50, 55]
  }],
  chart: {
    height: 100,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#35d1df', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-6"), options);
chart.render(); // sparkline-chart-7

var options = {
  series: [{
    name: "Series A",
    data: [48, 35, 60, 45, 55, 45]
  }],
  chart: {
    height: 100,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#fa3452', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-7"), options);
chart.render(); // sparkline-chart-8

var options = {
  series: [{
    name: "Series A",
    data: [42, 55, 30, 60, 40, 57]
  }],
  chart: {
    height: 100,
    type: 'area',
    sparkline: {
      enabled: true
    },
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100, 100, 100]
    }
  },
  colors: ['#34c38f', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#sparkline-chart-8"), options);
chart.render(); // area-charts-1

var options = {
  series: [{
    name: 'series1',
    data: [70, 60, 70, 65, 75, 70, 80]
  }],
  chart: {
    height: 320,
    type: 'area',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  grid: {
    borderColor: "#555",
    yaxis: {
      lines: {
        show: true
      }
    },
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#area-charts-1"), options);
chart.render(); // area-charts-2

var options = {
  series: [{
    name: 'series1',
    data: [50, 75, 60, 80, 55, 70, 60]
  }],
  chart: {
    height: 320,
    type: 'area',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  grid: {
    borderColor: "#555",
    yaxis: {
      lines: {
        show: true
      }
    },
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#area-charts-2"), options);
chart.render(); // area-charts-3

var options = {
  series: [{
    name: 'series1',
    data: [60, 45, 75, 60, 80, 75, 90]
  }],
  chart: {
    height: 320,
    type: 'area',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  grid: {
    borderColor: "#555",
    yaxis: {
      lines: {
        show: true
      }
    },
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#area-charts-3"), options);
chart.render(); // area-charts-4

var options = {
  series: [{
    name: 'series1',
    data: [70, 60, 70, 65, 75, 70, 80]
  }],
  chart: {
    height: 320,
    type: 'area',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  grid: {
    borderColor: "#555",
    yaxis: {
      lines: {
        show: true
      }
    },
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#area-charts-4"), options);
chart.render(); // area-charts-5

var options = {
  series: [{
    name: 'series1',
    data: [50, 75, 60, 80, 55, 70, 60]
  }],
  chart: {
    height: 320,
    type: 'area',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  grid: {
    borderColor: "#555",
    yaxis: {
      lines: {
        show: true
      }
    },
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  colors: ['#2a4fd7', 'transparent']
};
var chart = new ApexCharts(document.querySelector("#area-charts-5"), options);
chart.render(); // mixed-charts-1

var options = {
  series: [{
    name: 'Headphone',
    data: [40, 60, 40, 80, 20, 40, 20, 60, 20, 40, 60, 40]
  }, {
    name: 'Mobiles',
    data: [20, 40, 20, 20, 40, 60, 40, 20, 40, 20, 40, 20]
  }, {
    name: 'Accessories',
    data: [20, 40, 20, 40, 20, 40, 20, 40, 20, 40, 20, 40]
  }, {
    name: 'LED TV',
    data: [20, 40, 20, 40, 20, 40, 20, 40, 20, 40, 20, 40]
  }],
  chart: {
    type: 'bar',
    height: 250,
    stacked: true,
    stackType: '100%',
    toolbar: {
      autoSelected: "pan",
      show: false
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '18%',
      endingShape: 'rounded'
    }
  },
  dataLabels: {
    enabled: false
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  fill: {
    opacity: 1
  },
  legend: {
    show: false
  },
  responsive: [{
    breakpoint: 576,
    options: {
      plotOptions: {
        bar: {
          columnWidth: '45%'
        }
      },
      stroke: {
        width: 2
      }
    }
  }],
  colors: ['#ff556f', '#486fff', '#ffcf7e', '#e9eef2']
};
var chart = new ApexCharts(document.querySelector("#mixed-charts-1"), options);
chart.render();

/***/ }),

/***/ 5:
/*!****************************************************!*\
  !*** multi ./resources/js/pages/dashboard.init.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/erevenue/erevenue-2025/resources/js/pages/dashboard.init.js */"./resources/js/pages/dashboard.init.js");


/***/ })

/******/ });