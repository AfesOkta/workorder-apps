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
/******/ 	return __webpack_require__(__webpack_require__.s = 28);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/profile.init.js":
/*!********************************************!*\
  !*** ./resources/js/pages/profile.init.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

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
  colors: ['#2a4fd7']
};
var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-1"), options7);
chart7.render(); // sparkline-chart-2

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
  colors: ['#2a4fd7']
};
var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-2"), options7);
chart7.render(); // sparkline-chart-3

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
  colors: ['#2a4fd7']
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
  colors: ['#2a4fd7']
};
var chart7 = new ApexCharts(document.querySelector("#sparkline-chart-4"), options7);
chart7.render(); // column-chart-1

var options = {
  series: [{
    name: "Sale Product",
    data: [20, 30, 20, 40, 30, 50, 40, 60, 50, 70, 60, 80]
  }, {
    name: "Stock Product",
    data: [10, 15, 10, 20, 15, 25, 20, 30, 25, 35, 30, 40]
  }],
  chart: {
    type: "bar",
    height: 312,
    toolbar: {
      show: false
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "25%",
      endingShape: "rounded"
    }
  },
  legend: {
    position: "top",
    horizontalAlign: "right",
    offsetX: 0,
    offsetY: 0
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 3,
    colors: ["transparent"]
  },
  xaxis: {
    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
  },
  yaxis: {
    labels: {
      formatter: function formatter(value) {
        return value + "k";
      }
    }
  },
  fill: {
    opacity: 1
  },
  colors: ["#5071ea", "#cad3f5"]
};
var chart = new ApexCharts(document.querySelector("#column-chart-1"), options);
chart.render();

/***/ }),

/***/ 28:
/*!**************************************************!*\
  !*** multi ./resources/js/pages/profile.init.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/erevenue/erevenue-2025/resources/js/pages/profile.init.js */"./resources/js/pages/profile.init.js");


/***/ })

/******/ });