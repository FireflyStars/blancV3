(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_statistics_Statistics_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
/* harmony import */ var _store_types_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../store/types/types */ "./resources/js/store/types/types.js");


 // import vClickOutside from 'click-outside-vue3';

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "DatePicker",
  // directives: {
  //     clickOutside: vClickOutside.directive
  // },
  props: {
    modelValue: String,
    droppos: Object,
    label: String,
    disabled: Boolean,
    hint: String,
    availableDates: Array,
    disabledToDate: String,
    disabledFromDate: String,
    name: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue', 'loadtranche'],
  setup: function setup(props, context) {
    var store = (0,vuex__WEBPACK_IMPORTED_MODULE_2__.useStore)();
    var default_date = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)([]);
    var displayed_months_rows = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});
    var displayed_year_rows = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});
    var sel = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(function () {
      return store.getters["".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.GET_CURRENT_SELECT)];
    });
    var days = [{
      dayName: 'Mo',
      jsDay: 1
    }, {
      dayName: 'Tu',
      jsDay: 2
    }, {
      dayName: 'We',
      jsDay: 3
    }, {
      dayName: 'Th',
      jsDay: 4
    }, {
      dayName: 'Fr',
      jsDay: 5
    }, {
      dayName: 'Sa',
      jsDay: 6
    }, {
      dayName: 'Su',
      jsDay: 0
    }];
    var monthsName = {
      0: {
        name: "January"
      },
      1: {
        name: "Febuary"
      },
      2: {
        name: "March"
      },
      3: {
        name: "April"
      },
      4: {
        name: "May"
      },
      5: {
        name: "June"
      },
      6: {
        name: "July"
      },
      7: {
        name: "August"
      },
      8: {
        name: "September"
      },
      9: {
        name: "October"
      },
      10: {
        name: "November"
      },
      11: {
        name: "December"
      }
    };
    var currentView = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('dates');
    var MonthYear = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});
    var formated_date = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('');

    if (props.modelValue !== "") {
      default_date.value = props.modelValue.split('-');
      MonthYear.value.month = default_date.value[1] - 1;
      MonthYear.value.year = parseInt(default_date.value[0]);
    } else {
      var d = new Date();
      MonthYear.value.month = d.getMonth();
      MonthYear.value.year = d.getFullYear();
    }

    function minusMonth() {
      if (MonthYear.value.month == 0) {
        MonthYear.value.month = 11;
        MonthYear.value.year--;
      } else {
        MonthYear.value.month--;
      }
    }

    function plusMonth() {
      if (MonthYear.value.month == 11) {
        MonthYear.value.month = 0;
        MonthYear.value.year++;
      } else {
        MonthYear.value.month++;
      }
    }

    var displayed_dates = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)([]);
    var displayed_dates_rows = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});

    function renderPicker() {
      if (typeof default_date.value[0] != "undefined" && typeof default_date.value[1] != "undefined" && typeof default_date.value[2] != "undefined") formated_date.value = "".concat(default_date.value[1], "/").concat(default_date.value[2], "/").concat(default_date.value[0]);
      displayed_dates_rows.value = {
        0: [],
        1: [],
        2: [],
        3: [],
        4: [],
        5: []
      };
      displayed_dates.value = [];
      var firstDayofMonth = new Date(MonthYear.value.year, MonthYear.value.month, 1).getDay();
      var lastDateofMonth = new Date(MonthYear.value.year, MonthYear.value.month + 1, 0).getDate();
      var lastMonth = MonthYear.value.month == 0 ? 11 : MonthYear.value.month - 1;
      var lastMonthYear = MonthYear.value.month == 0 ? MonthYear.value.year - 1 : MonthYear.value.year;
      var lastMonthEnd = new Date(lastMonthYear, lastMonth + 1, 0).getDate();
      var calendarStarts = lastMonthEnd - firstDayofMonth + 2;
      var notavailable = typeof props.availableDates != "undefined" && props.availableDates.length > 0;

      while (calendarStarts <= lastMonthEnd) {
        displayed_dates.value.push({
          date: calendarStarts,
          month: lastMonth,
          year: lastMonthYear,
          current_month: false,
          selected: false,
          notavailable: notavailable
        });
        calendarStarts++;
      }

      var date = 1;

      while (date <= lastDateofMonth) {
        displayed_dates.value.push({
          date: date,
          month: MonthYear.value.month,
          year: MonthYear.value.year,
          current_month: true,
          selected: false,
          notavailable: notavailable
        });
        date++;
      }

      date = 1;

      while (displayed_dates.value.length < 35) {
        displayed_dates.value.push({
          date: date,
          month: MonthYear.value.month == 11 ? 0 : MonthYear.value.month + 1,
          year: MonthYear.value.month == 11 ? MonthYear.value.year + 1 : MonthYear.value.year,
          current_month: false,
          selected: false,
          notavailable: notavailable
        });
        date++;
      }

      date = 1;

      if (displayed_dates.value.length == 36) {
        while (displayed_dates.value.length < 42) {
          displayed_dates.value.push({
            date: date,
            month: MonthYear.value.month == 11 ? 0 : MonthYear.value.month + 1,
            year: MonthYear.value.month == 11 ? MonthYear.value.year + 1 : MonthYear.value.year,
            current_month: false,
            selected: false,
            notavailable: notavailable
          });
          date++;
        }
      }

      var index = 0;
      var count = 0;
      var datestr = '';

      for (var i in displayed_dates.value) {
        datestr = "".concat(displayed_dates.value[i].year, "-").concat((parseInt(displayed_dates.value[i].month) + 1).toString().padStart(2, "0"), "-").concat(displayed_dates.value[i].date.toString().padStart(2, "0"));
        if (typeof props.availableDates != "undefined" && props.availableDates.includes(datestr)) displayed_dates.value[i].notavailable = false; //disabledToDate

        if (typeof props.disabledToDate != "undefined" && props.disabledToDate != "") {
          var disabledto = new Date(props.disabledToDate);
          var curdate = new Date(datestr);

          if (curdate <= disabledto) {
            displayed_dates.value[i].notavailable = true;
          }
        } //disabledFromDate


        if (typeof props.disabledFromDate != "undefined" && props.disabledFromDate != "") {
          var disabledFrom = new Date(props.disabledFromDate);

          var _curdate = new Date(datestr);

          if (_curdate > disabledFrom) {
            displayed_dates.value[i].notavailable = true;
          }
        }

        displayed_dates.value[i].selected = false;

        if (count == 7) {
          index++;
          count = 0;
        }

        if (default_date.value.length > 0) {
          if (displayed_dates.value[i].date == parseInt(default_date.value[2]) && displayed_dates.value[i].month + 1 == parseInt(default_date.value[1]) && displayed_dates.value[i].year == parseInt(default_date.value[0])) {
            displayed_dates.value[i].selected = true;
          }
        }

        displayed_dates_rows.value[index].push(displayed_dates.value[i]);
        count++;
      }
    }

    renderPicker();
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return _.cloneDeep(MonthYear);
    }, function (current_val, previous_val) {
      renderPicker();
      renderMonthsView();
      renderYearsView();
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return _.cloneDeep(default_date);
    }, function (current_val, previous_val) {
      renderPicker();
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return props.disabledToDate;
    }, function (current_val, previous_val) {
      renderPicker();
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return props.disabledFromDate;
    }, function (current_val, previous_val) {
      renderPicker();
    });

    function setDate(y, m, d) {
      default_date.value[0] = parseInt(y);
      default_date.value[1] = parseInt(m) + 1;
      default_date.value[2] = parseInt(d);
      context.emit("update:modelValue", "".concat(default_date.value[0], "-").concat(default_date.value[1].toString().padStart(2, "0"), "-").concat(default_date.value[2].toString().padStart(2, "0")));
      toggleshowDp();
      context.emit("loadtranche");
    }

    function setMonth(m) {
      // default_date.value[1]=parseInt(m)+1;
      console.log('called from parent');
      MonthYear.value.month = parseInt(m);
      currentView.value = 'dates';
    }

    function setYear(y) {
      MonthYear.value.year = parseInt(y);
      currentView.value = 'dates';
    }

    function showYears() {
      currentView.value = 'years';
      renderYearsView();
    }

    function showMonths() {
      currentView.value = 'months';
      renderMonthsView();
    }

    function renderMonthsView() {
      displayed_months_rows.value = {
        0: [],
        1: [],
        2: [],
        3: []
      };
      var index = 0;
      var count = 0;

      for (var jsMonth in monthsName) {
        monthsName[jsMonth].current = false;
        monthsName[jsMonth].jsMonth = jsMonth;
        if (MonthYear.value.month == jsMonth) monthsName[jsMonth].current = true;

        if (count == 3) {
          index++;
          count = 0;
        }

        displayed_months_rows.value[index].push(monthsName[jsMonth]);
        count++;
      }
    }

    function renderYearsView() {
      displayed_year_rows.value = {
        0: [],
        1: [],
        2: []
      };
      var index = 0;
      var count = 0;
      var startYr = MonthYear.value.year - 4;
      var endYr = MonthYear.value.year + 4;

      while (startYr <= endYr) {
        if (count == 3) {
          index++;
          count = 0;
        }

        displayed_year_rows.value[index].push({
          year: startYr,
          current: MonthYear.value.year == startYr
        });
        count++;
        startYr++;
      }
    }

    function toggleshowDp() {
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.nextTick)(function () {
        store.commit("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SET_CURRENT_SELECT), sel.value === props.name ? '' : props.name);
      }).then(function () {
        sel = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(function () {
          return store.getters["".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.GET_CURRENT_SELECT)];
        });
      });
    }

    function resetPicker() {
      console.log('reset called from parent');
      default_date.value = [];
    }

    return {
      sel: sel,
      minusMonth: minusMonth,
      plusMonth: plusMonth,
      MonthYear: MonthYear,
      days: days,
      setDate: setDate,
      formated_date: formated_date,
      displayed_dates: displayed_dates,
      monthsName: monthsName,
      displayed_year_rows: displayed_year_rows,
      displayed_months_rows: displayed_months_rows,
      displayed_dates_rows: displayed_dates_rows,
      currentView: currentView,
      showMonths: showMonths,
      showYears: showYears,
      setMonth: setMonth,
      setYear: setYear,
      toggleshowDp: toggleshowDp,
      resetPicker: resetPicker,
      renderPicker: renderPicker
    };
  },
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
/* harmony import */ var _store_types_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../store/types/types */ "./resources/js/store/types/types.js");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "SelectOptions",
  props: {
    hint: String,
    placeholder: String,
    name: {
      type: String,
      required: true
    },
    options: Object,
    modelValue: String | Number,
    classnames: String,
    label: String,
    disabled: Boolean,
    valid: Boolean | null
  },
  setup: function setup(props, context) {
    var store = (0,vuex__WEBPACK_IMPORTED_MODULE_2__.useStore)();
    var current = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('');
    var current_display = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('');
    var sel = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(function () {
      return store.getters["".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.GET_CURRENT_SELECT)];
    });

    var selectclick = function selectclick() {
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.nextTick)(function () {
        store.commit("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SET_CURRENT_SELECT), sel.value === props.name ? '' : props.name);
      }).then(function () {
        sel = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(function () {
          return store.getters["".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.GET_CURRENT_SELECT)];
        });
      });
    };

    var select = function select(index) {
      current.value = index;
      context.emit("update:modelValue", props.options[index].value);
      store.commit("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SET_CURRENT_SELECT), '');
    };

    var currentoption = props.options.filter(function (option) {
      return option.value.toString() == props.modelValue.toString();
    });
    currentoption = _.cloneDeep(currentoption);

    if (typeof currentoption[0] != "undefined") {
      current_display.value = currentoption[0].display;
    }

    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return props.modelValue;
    }, function (current_val, previous_val) {
      if (current_val == "") current_display.value = "";
      var currentoption = props.options.filter(function (option) {
        return option.value.toString() == props.modelValue.toString();
      });
      currentoption = _.cloneDeep(currentoption);

      if (typeof currentoption[0] != "undefined") {
        current_display.value = currentoption[0].display;
      }
    });
    var cname = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('');

    var classNames = function classNames() {
      return "".concat(typeof props.classnames != 'undefined' ? props.classnames : '', " ").concat(sel.value === props.name ? 'active' : '', " ").concat(current_display.value != '' ? 'selected' : '', " ").concat(props.disabled == true ? 'disabled' : '', " ").concat(props.valid === true ? 'valid' : props.valid === false ? 'invalid' : '');
    };

    cname.value = classNames();
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return sel.value;
    }, function (current_val, previous_val) {
      cname.value = classNames();
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return current_display.value;
    }, function (current_val, previous_val) {
      cname.value = classNames();
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return props.disabled;
    }, function (current_val, previous_val) {
      cname.value = classNames();
      store.commit("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SELECT_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_1__.SET_CURRENT_SELECT), '');
    });
    return {
      selectclick: selectclick,
      select: select,
      current: current,
      current_display: current_display,
      cname: cname,
      sel: sel
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js ***!
  \*************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
/* harmony import */ var _miscellaneous_CheckBox__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../miscellaneous/CheckBox */ "./resources/js/components/miscellaneous/CheckBox.vue");
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
/* harmony import */ var _miscellaneous_SelectOptions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../miscellaneous/SelectOptions */ "./resources/js/components/miscellaneous/SelectOptions.vue");
/* harmony import */ var _miscellaneous_DatePicker__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../miscellaneous/DatePicker */ "./resources/js/components/miscellaneous/DatePicker.vue");
/* harmony import */ var _miscellaneous_SwitchBtn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../miscellaneous/SwitchBtn */ "./resources/js/components/miscellaneous/SwitchBtn.vue");





 // import vClickOutside from 'click-outside-vue3';

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "StatisticsFilters",
  directives: {// clickOutside: vClickOutside.directive
  },
  props: ['filterVal'],
  emits: ['update:filterVal'],
  components: {
    CheckBox: _miscellaneous_CheckBox__WEBPACK_IMPORTED_MODULE_1__.default,
    SelectOptions: _miscellaneous_SelectOptions__WEBPACK_IMPORTED_MODULE_2__.default,
    DatePicker: _miscellaneous_DatePicker__WEBPACK_IMPORTED_MODULE_3__.default,
    SwitchBtn: _miscellaneous_SwitchBtn__WEBPACK_IMPORTED_MODULE_4__.default
  },
  setup: function setup(props, context) {
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var current = new Date();
    var currentDate = "".concat(current.getFullYear(), "-").concat(current.getMonth() + 1, "-").concat(current.getDate());
    var showfilter = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    var customFilter = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    var selectedValue = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('Today');
    var startDate = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(currentDate);
    var endDate = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(currentDate);
    var startDisabledtodate = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(currentDate);
    var endDisabledtodate = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(currentDate);
    var customFilterText = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)('');
    var store = (0,vuex__WEBPACK_IMPORTED_MODULE_5__.useStore)();

    var toggleShow = function toggleShow() {
      return showfilter.value = !showfilter.value;
    };

    var hasActiveFilters = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(function () {
      return false;
    });
    var dateRange = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)([]);
    dateRange.value = [{
      value: "Today",
      display: "Today"
    }, {
      value: "Yesterday",
      display: "Yesterday"
    }, {
      value: "Last 7 days",
      display: "Last 7 days"
    }, {
      value: "Last 90 days",
      display: "Last 90 days"
    }, {
      value: "Last Month",
      display: "Last Month"
    }, {
      value: "Year to date",
      display: "Year to date"
    }, {
      value: "4th Quarter",
      display: "4th Quarter (" + "".concat(current.getFullYear() - 1) + ")"
    }, {
      value: "3rd Quarter",
      display: "3rd Quarter (" + "".concat(current.getFullYear() - 1) + ")"
    }, {
      value: "2nd Quarter",
      display: "2nd Quarter (" + "".concat(current.getFullYear() - 1) + ")"
    }, {
      value: "1st Quarter",
      display: "1st Quarter (" + "".concat(current.getFullYear() - 1) + ")"
    }];

    function applyFilter() {
      var data = {
        customFilter: customFilter.value,
        startDate: startDate.value,
        endDate: endDate.value,
        dateRangeType: selectedValue.value
      };
      customFilterText.value = "Compared From ".concat(new Date(startDate.value).getDate(), " ").concat(monthNames[new Date(startDate.value).getMonth()], ", ").concat(new Date(startDate.value).getFullYear(), " To ").concat(new Date(endDate.value).getDate(), " ").concat(monthNames[new Date(endDate.value).getMonth()], ", ").concat(new Date(endDate.value).getFullYear());
      context.emit("update:filterVal", data); // store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_RESET_MULITCHECKED}`);
      // store.dispatch(`${ORDERLIST_MODULE}${ORDERLIST_FILTER}`,_.cloneDeep(preselection.value));
      // current_filter.value='';

      toggleShow();
    }

    function cancel() {
      // current_filter.value='';
      // const filters=store.getters[`${ORDERLIST_MODULE}${ORDERLIST_GET_FILTER}`];
      // preselection.value=_.cloneDeep(filters);
      toggleShow();
    }

    return {
      toggleShow: toggleShow,
      applyFilter: applyFilter,
      cancel: cancel,
      dateRange: dateRange,
      showfilter: showfilter,
      customFilter: customFilter,
      hasActiveFilters: hasActiveFilters,
      selectedValue: selectedValue,
      startDate: startDate,
      startDisabledtodate: startDisabledtodate,
      endDate: endDate,
      endDisabledtodate: endDisabledtodate,
      customFilterText: customFilterText
    };
  },
  methods: {
    onClickOutside: function onClickOutside(event) {
      this.showfilter = false;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "Switch",
  props: {
    modelValue: Boolean,
    labelLeft: String,
    disabled: Boolean,
    labelRight: String
  },
  setup: function setup(props, context) {
    var switchval = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    switchval.value = props.modelValue;
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(function () {
      return props.modelValue;
    }, function (current_val, previous_val) {
      switchval.value = current_val;
    });

    var toggle = function toggle() {
      switchval.value = !switchval.value;
      context.emit("update:modelValue", switchval.value);
    };

    return {
      switchval: switchval,
      toggle: toggle
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _layout_SideBar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../layout/SideBar */ "./resources/js/components/layout/SideBar.vue");
/* harmony import */ var _layout_MainHeader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../layout/MainHeader */ "./resources/js/components/layout/MainHeader.vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
/* harmony import */ var vue_router__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! vue-router */ "./node_modules/vue-router/dist/vue-router.esm-bundler.js");
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
/* harmony import */ var _store_types_types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../store/types/types */ "./resources/js/store/types/types.js");
/* harmony import */ var _miscellaneous_StatisticsFilters__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../miscellaneous/StatisticsFilters */ "./resources/js/components/miscellaneous/StatisticsFilters.vue");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) { symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); } keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }








/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "Statistics",
  components: {
    SideBar: _layout_SideBar__WEBPACK_IMPORTED_MODULE_0__.default,
    MainHeader: _layout_MainHeader__WEBPACK_IMPORTED_MODULE_1__.default,
    StatisticsFilters: _miscellaneous_StatisticsFilters__WEBPACK_IMPORTED_MODULE_4__.default
  },
  setup: function setup(props, context) {
    var store = (0,vuex__WEBPACK_IMPORTED_MODULE_5__.useStore)();
    var statistique = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)({});
    var stats_today = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)({});
    var top_3_today = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)({});
    var width_scale = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)(0);
    var selectedValue = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)("");
    var filterVal = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)({});
    var today = new Date();
    filterVal.value = {
      customFilter: 0,
      startDate: "".concat(today.getFullYear(), "-").concat(today.getMonth() + 1, "-").concat(today.getDate()),
      endDate: "".concat(today.getFullYear(), "-").concat(today.getMonth() + 1, "-").concat(today.getDate()),
      dateRangeType: 'Today'
    };
    var router = (0,vue_router__WEBPACK_IMPORTED_MODULE_6__.useRouter)();
    var route = (0,vue_router__WEBPACK_IMPORTED_MODULE_6__.useRoute)();
    var route_name = (0,vue__WEBPACK_IMPORTED_MODULE_2__.ref)(route.name);
    (0,vue__WEBPACK_IMPORTED_MODULE_2__.watch)(function () {
      return route.name;
    }, function (current_val, previous_val) {
      route_name.value = current_val;
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_2__.watch)(function () {
      return filterVal.value;
    }, function (current_val, previous_val) {
      store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.LOADER_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.DISPLAY_LOADER), [true, 'Loading data...']);
      store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_LOAD_LIST), current_val).then(function (response) {
        console.log(response.data);
        statistique.value = response.data.statistique; // stats_today.value = response.data.statistique.stats_today;
        // top_3_today.value = response.data.statistique.top_3_today;
        // width_scale.value = response.data.statistique.scale;
      })["finally"](function () {
        store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.LOADER_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.HIDE_LOADER));
      });
    });
    store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.LOADER_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.DISPLAY_LOADER), [true, 'Loading data...']);
    store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_LOAD_LIST), filterVal.value).then(function (response) {
      console.log(response.data);
      statistique.value = response.data.statistique; // stats_today.value = response.data.statistique.stats_today;
      // top_3_today.value = response.data.statistique.top_3_today;
      // width_scale.value = response.data.statistique.scale;                
      // console.log(response.data.statistique)
    })["finally"](function () {
      store.dispatch("".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.LOADER_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.HIDE_LOADER));
    });
    return {
      statistics: (0,vue__WEBPACK_IMPORTED_MODULE_2__.computed)(function () {
        return store.getters["".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.GET_STATISTICS)];
      }),
      selectedValue: selectedValue,
      filterVal: filterVal,
      stats_today: stats_today,
      top_3_today: top_3_today,
      width_scale: width_scale,
      route_name: route_name,
      router: router,
      statistique: statistique
    };
  },
  methods: _objectSpread(_objectSpread({}, (0,vuex__WEBPACK_IMPORTED_MODULE_5__.mapActions)({
    STATISTICS_LOAD_LIST: "".concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_MODULE).concat(_store_types_types__WEBPACK_IMPORTED_MODULE_3__.STATISTICS_LOAD_LIST)
  })), {}, {
    onClickOutside: function onClickOutside(event) {}
  })
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


var _withId = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.withScopeId)("data-v-6cc0b0f0");

(0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-6cc0b0f0");

var _hoisted_1 = {
  "class": "dp noselect"
};
var _hoisted_2 = {
  "class": "row"
};
var _hoisted_3 = {
  "class": "col-2"
};
var _hoisted_4 = {
  "class": "col-8 text-center"
};
var _hoisted_5 = {
  "class": "col-2"
};
var _hoisted_6 = {
  key: 0,
  "class": "position-absolute mw-picker"
};
var _hoisted_7 = {
  "class": "row"
};
var _hoisted_8 = {
  key: 0
};
var _hoisted_9 = {
  key: 0
};
var _hoisted_10 = {
  key: 0,
  "class": "hint"
};

(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)();

var render = /*#__PURE__*/_withId(function (_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_1, [$props.label ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("label", {
    key: 0,
    "class": ["select-label", {
      disabled: $props.disabled == true
    }]
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.label), 3
  /* TEXT, CLASS */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("input", {
    type: "text",
    readonly: "",
    placeholder: "Day",
    "onUpdate:modelValue": _cache[1] || (_cache[1] = function ($event) {
      return $setup.formated_date = $event;
    }),
    onClick: _cache[2] || (_cache[2] = function () {
      return $setup.toggleshowDp && $setup.toggleshowDp.apply($setup, arguments);
    })
  }, null, 512
  /* NEED_PATCH */
  ), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.formated_date]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
    name: "trans-dp-picker"
  }, {
    "default": _withId(function () {
      return [$setup.sel === $props.name ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
        key: 0,
        "class": ["dp-picker", {
          row6: $setup.displayed_dates_rows[5].length > 0 && $setup.currentView == 'dates'
        }],
        style: {
          top: $props.droppos.top,
          right: $props.droppos.right,
          bottom: $props.droppos.bottom,
          left: $props.droppos.left,
          transformOrigin: $props.droppos.transformOrigin
        }
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        onClick: _cache[3] || (_cache[3] = function () {
          return $setup.minusMonth && $setup.minusMonth.apply($setup, arguments);
        }),
        "class": "btn btn-dp minus"
      })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        "class": "btn btn-month",
        onClick: _cache[4] || (_cache[4] = function () {
          return $setup.showMonths && $setup.showMonths.apply($setup, arguments);
        })
      }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.monthsName[$setup.MonthYear.month].name), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        "class": "btn btn-year",
        onClick: _cache[5] || (_cache[5] = function () {
          return $setup.showYears && $setup.showYears.apply($setup, arguments);
        })
      }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.MonthYear.year), 1
      /* TEXT */
      )]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_5, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        onClick: _cache[6] || (_cache[6] = function () {
          return $setup.plusMonth && $setup.plusMonth.apply($setup, arguments);
        }),
        "class": "btn btn-dp "
      })])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
        name: "trans-dp-picker-zoom"
      }, {
        "default": _withId(function () {
          return [$setup.currentView == 'dates' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_7, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.days, function (day) {
            return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
              "class": "col dp-dayname",
              key: day
            }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(day.dayName), 1
            /* TEXT */
            );
          }), 128
          /* KEYED_FRAGMENT */
          ))]), ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.displayed_dates_rows, function (row) {
            return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
              "class": "row",
              key: row
            }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(row, function (day, index) {
              return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
                key: index,
                "class": ["col dp-dates", {
                  disabled: !day.current_month,
                  current: day.selected,
                  notavailable: day.notavailable
                }],
                onClick: function onClick($event) {
                  return $setup.setDate(day.year, day.month, day.date);
                }
              }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(day.date), 11
              /* TEXT, CLASS, PROPS */
              , ["onClick"]);
            }), 128
            /* KEYED_FRAGMENT */
            ))]);
          }), 128
          /* KEYED_FRAGMENT */
          ))])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
        }),
        _: 1
        /* STABLE */

      }), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
        name: "trans-dp-picker-zoom",
        "class": "position-absolute mw-picker"
      }, {
        "default": _withId(function () {
          return [$setup.currentView == 'months' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_8, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.displayed_months_rows, function (row, i) {
            return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
              "class": ["row row-months", {
                'mt-5': i == 0
              }],
              key: i
            }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(row, function (month, index) {
              return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
                key: index,
                "class": ["col dp-months", {
                  current: month.current
                }],
                onClick: function onClick($event) {
                  return $setup.setMonth(month.jsMonth);
                }
              }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(month.name.substr(0, 3)), 11
              /* TEXT, CLASS, PROPS */
              , ["onClick"]);
            }), 128
            /* KEYED_FRAGMENT */
            ))], 2
            /* CLASS */
            );
          }), 128
          /* KEYED_FRAGMENT */
          ))])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
        }),
        _: 1
        /* STABLE */

      }), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
        name: "trans-dp-picker-zoom",
        "class": "position-absolute mw-picker"
      }, {
        "default": _withId(function () {
          return [$setup.currentView == 'years' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_9, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.displayed_year_rows, function (row, i) {
            return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
              "class": ["row row-years", {
                'mt-5': i == 0
              }],
              key: i
            }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(row, function (y, index) {
              return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
                key: index,
                "class": ["col dp-years", {
                  current: y.current
                }],
                onClick: function onClick($event) {
                  return $setup.setYear(y.year);
                }
              }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(y.year), 11
              /* TEXT, CLASS, PROPS */
              , ["onClick"]);
            }), 128
            /* KEYED_FRAGMENT */
            ))], 2
            /* CLASS */
            );
          }), 128
          /* KEYED_FRAGMENT */
          ))])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
        }),
        _: 1
        /* STABLE */

      })], 6
      /* CLASS, STYLE */
      )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    _: 1
    /* STABLE */

  })]), $props.hint ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_10, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.hint), 1
  /* TEXT */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 64
  /* STABLE_FRAGMENT */
  );
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


var _withId = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.withScopeId)("data-v-31289606");

(0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-31289606");

var _hoisted_1 = {
  key: 0,
  "class": "select-options"
};
var _hoisted_2 = {
  key: 1,
  "class": "hint"
};

(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)();

var render = /*#__PURE__*/_withId(function (_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [$props.label ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("label", {
    key: 0,
    "class": ["select-label body_medium", {
      disabled: $props.disabled == true
    }]
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.label), 3
  /* TEXT, CLASS */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
    "class": ["select noselect", $setup.cname],
    onClick: _cache[2] || (_cache[2] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $setup.selectclick && $setup.selectclick.apply($setup, arguments);
    }, ["self"]))
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("span", {
    "class": ["disp", {
      placeholder: $setup.current_display == '',
      disabled: $props.disabled == true
    }],
    onClick: _cache[1] || (_cache[1] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $setup.selectclick && $setup.selectclick.apply($setup, arguments);
    }, ["self"]))
  }, [$setup.current_display == '' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: 0
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.placeholder), 1
  /* TEXT */
  )], 2112
  /* STABLE_FRAGMENT, DEV_ROOT_FRAGMENT */
  )) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: 1
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.current_display), 1
  /* TEXT */
  )], 2112
  /* STABLE_FRAGMENT, DEV_ROOT_FRAGMENT */
  ))], 2
  /* CLASS */
  ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
    name: "trans-select"
  }, {
    "default": _withId(function () {
      return [$setup.sel === $props.name ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default", {}, function () {
        return [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.options, function (item, index) {
          return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
            "class": "opts",
            key: index,
            onClick: function onClick($event) {
              return $setup.select(index);
            }
          }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(item.display), 9
          /* TEXT, PROPS */
          , ["onClick"]);
        }), 128
        /* KEYED_FRAGMENT */
        ))];
      }, {}, true)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    _: 3
    /* FORWARDED */

  })], 2
  /* CLASS */
  ), $props.hint ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_2, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.hint), 1
  /* TEXT */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 64
  /* STABLE_FRAGMENT */
  );
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


var _withId = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.withScopeId)("data-v-5c5adc5c");

(0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-5c5adc5c");

var _hoisted_1 = {
  "class": "row"
};
var _hoisted_2 = {
  "class": "col-1 width-fit-content"
};

var _hoisted_3 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("i", {
  "class": "bi bi-calendar"
}, null, -1
/* HOISTED */
);

var _hoisted_4 = {
  "class": "col-10 custom-filter-text"
};
var _hoisted_5 = {
  key: 0
};
var _hoisted_6 = {
  key: 0,
  "class": "filters position-absolute"
};

var _hoisted_7 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("h2", {
  "class": "subtitle"
}, "Date range", -1
/* HOISTED */
);

var _hoisted_8 = {
  "class": "row"
};
var _hoisted_9 = {
  key: 0,
  "class": "row mt-10"
};
var _hoisted_10 = {
  "class": "col-10"
};
var _hoisted_11 = {
  key: 1
};
var _hoisted_12 = {
  "class": "row mt-10"
};
var _hoisted_13 = {
  "class": "col-3"
};
var _hoisted_14 = {
  "class": "row mt-10"
};
var _hoisted_15 = {
  "class": "col-3"
};
var _hoisted_16 = {
  "class": "row buttons"
};
var _hoisted_17 = {
  "class": "col text-center"
};
var _hoisted_18 = {
  "class": "col text-right"
};

(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)();

var render = /*#__PURE__*/_withId(function (_ctx, _cache, $props, $setup, $data, $options) {
  var _component_switch_btn = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("switch-btn");

  var _component_select_options = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("select-options");

  var _component_date_picker = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("date-picker");

  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
    "class": ["btn btn-outline-dark filter body_small_bold", {
      active: $setup.showfilter,
      hasfilters: $setup.hasActiveFilters
    }],
    onClick: _cache[1] || (_cache[1] = function () {
      return $setup.toggleShow && $setup.toggleShow.apply($setup, arguments);
    })
  }, [_hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.customFilter ? 'Custom' : $setup.selectedValue), 1
  /* TEXT */
  )], 2
  /* CLASS */
  )]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_4, [$setup.customFilter ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("p", _hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.customFilterText), 1
  /* TEXT */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
    name: "trans-filter"
  }, {
    "default": _withId(function () {
      return [$setup.showfilter ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_6, [_hoisted_7, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_switch_btn, {
        modelValue: $setup.customFilter,
        "onUpdate:modelValue": _cache[2] || (_cache[2] = function ($event) {
          return $setup.customFilter = $event;
        }),
        "label-left": "Custom Filter",
        "label-right": ""
      }, null, 8
      /* PROPS */
      , ["modelValue"])]), !$setup.customFilter ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_select_options, {
        modelValue: $setup.selectedValue,
        "onUpdate:modelValue": _cache[3] || (_cache[3] = function ($event) {
          return $setup.selectedValue = $event;
        }),
        options: $setup.dateRange,
        name: "select-date-range",
        classnames: "black-border"
      }, null, 8
      /* PROPS */
      , ["modelValue", "options"])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $setup.customFilter ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_12, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_date_picker, {
        modelValue: $setup.startDate,
        "onUpdate:modelValue": [_cache[4] || (_cache[4] = function ($event) {
          return $setup.startDate = $event;
        }), _cache[5] || (_cache[5] = function (newValue) {
          return $setup.startDate = newValue;
        })],
        name: "start_date",
        droppos: {
          top: 'auto',
          right: 'auto',
          bottom: 'auto',
          left: '0',
          transformOrigin: 'top right'
        },
        label: "Starting",
        "disabled-from-date": $setup.startDisabledtodate
      }, null, 8
      /* PROPS */
      , ["modelValue", "disabled-from-date"])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_14, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_15, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_date_picker, {
        modelValue: $setup.endDate,
        "onUpdate:modelValue": [_cache[6] || (_cache[6] = function ($event) {
          return $setup.endDate = $event;
        }), _cache[7] || (_cache[7] = function (newValue) {
          return $setup.endDate = newValue;
        })],
        name: "end_date",
        droppos: {
          top: 'auto',
          right: 'auto',
          bottom: 'auto',
          left: '0',
          transformOrigin: 'top right'
        },
        label: "Ending",
        "disabled-from-date": $setup.endDisabledtodate
      }, null, 8
      /* PROPS */
      , ["modelValue", "disabled-from-date"])])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_16, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_17, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        "class": "btn btn-link  body_regular",
        onClick: _cache[8] || (_cache[8] = function () {
          return $setup.cancel && $setup.cancel.apply($setup, arguments);
        })
      }, "Cancel")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_18, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("button", {
        "class": "btn btn-dark body_medium",
        onClick: _cache[9] || (_cache[9] = function () {
          return $setup.applyFilter && $setup.applyFilter.apply($setup, arguments);
        })
      }, "Apply")])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    _: 1
    /* STABLE */

  })], 64
  /* STABLE_FRAGMENT */
  );
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


var _withId = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.withScopeId)("data-v-c68a14e8");

(0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-c68a14e8");

var _hoisted_1 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("span", {
  "class": "body_medium noselect"
}, "Yes", -1
/* HOISTED */
);

var _hoisted_2 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("span", {
  "class": "body_medium noselect"
}, "No", -1
/* HOISTED */
);

(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)();

var render = /*#__PURE__*/_withId(function (_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
    "class": ["switch-wrapper", {
      disabled: $props.disabled
    }]
  }, [$props.labelLeft ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("label", {
    key: 0,
    "class": "body_small",
    onClick: _cache[1] || (_cache[1] = function () {
      return $setup.toggle && $setup.toggle.apply($setup, arguments);
    })
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.labelLeft), 1
  /* TEXT */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
    "class": ["switch", {
      on: $setup.switchval
    }],
    onClick: _cache[2] || (_cache[2] = function () {
      return $setup.toggle && $setup.toggle.apply($setup, arguments);
    })
  }, [_hoisted_1, _hoisted_2], 2
  /* CLASS */
  ), $props.labelRight ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("label", {
    key: 1,
    "class": "body_small",
    onClick: _cache[3] || (_cache[3] = function () {
      return $setup.toggle && $setup.toggle.apply($setup, arguments);
    })
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.labelRight), 1
  /* TEXT */
  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 2
  /* CLASS */
  );
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18 ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = {
  "class": "container-fluid h-100 bg-color"
};
var _hoisted_2 = {
  "class": "row d-flex align-content-stretch align-items-stretch flex-row hmax",
  style: {
    "z-index": "100"
  }
};
var _hoisted_3 = {
  "class": "col main-view p-5"
};

var _hoisted_4 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("h2", null, "Statistics", -1
/* HOISTED */
);

var _hoisted_5 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "container-fluid orderlist-tabs d-flex align-items-center"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "orderlist-tab body_bold active"
}, "Sales")], -1
/* HOISTED */
);

var _hoisted_6 = {
  "class": "container-stat position-relative mb-10"
};
var _hoisted_7 = {
  "class": "row"
};
var _hoisted_8 = {
  "class": "col-10 mb-10"
};
var _hoisted_9 = {
  "class": "row pb-4"
};
var _hoisted_10 = {
  "class": "col-sm-4"
};
var _hoisted_11 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};
var _hoisted_12 = {
  "class": "row fw-bold"
};

var _hoisted_13 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-6 p-0 text-nowrap"
}, "Total Sales (by channel)", -1
/* HOISTED */
);

var _hoisted_14 = {
  "class": "col-4 float-right"
};
var _hoisted_15 = {
  "class": "row"
};

var _hoisted_16 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row fst-itali"
}, "Stores", -1
/* HOISTED */
);

var _hoisted_17 = {
  "class": "row text-center"
};
var _hoisted_18 = {
  "class": "first-bar col-3"
};
var _hoisted_19 = {
  "class": "second-bar col-3"
};
var _hoisted_20 = {
  "class": "third-bar col-3"
};
var _hoisted_21 = {
  "class": "fourth-bar col-3"
};

var _hoisted_22 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row text-center"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "MB"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "NH"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "CH"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "SK")], -1
/* HOISTED */
);

var _hoisted_23 = {
  "class": "row"
};

var _hoisted_24 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row fst-itali"
}, "Deliveries", -1
/* HOISTED */
);

var _hoisted_25 = {
  "class": "row text-center"
};
var _hoisted_26 = {
  "class": "first-bar col-6"
};
var _hoisted_27 = {
  "class": "fourth-bar col-6"
};

var _hoisted_28 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row text-center"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-6"
}, "B2C"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-6"
}, "B2B")], -1
/* HOISTED */
);

var _hoisted_29 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};
var _hoisted_30 = {
  "class": "row body_bold"
};

var _hoisted_31 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8"
}, "Average Order Value", -1
/* HOISTED */
);

var _hoisted_32 = {
  "class": "col-2 float-right"
};
var _hoisted_33 = {
  "class": "row"
};
var _hoisted_34 = {
  "class": "row"
};

var _hoisted_35 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Stores", -1
/* HOISTED */
);

var _hoisted_36 = {
  "class": "col-4 float-right width-fit-content"
};
var _hoisted_37 = {
  "class": "row"
};

var _hoisted_38 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Delivery", -1
/* HOISTED */
);

var _hoisted_39 = {
  "class": "col-4 float-right width-fit-content"
};
var _hoisted_40 = {
  "class": "row"
};

var _hoisted_41 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "B2B", -1
/* HOISTED */
);

var _hoisted_42 = {
  "class": "col-4 float-right width-fit-content"
};
var _hoisted_43 = {
  "class": "row"
};

var _hoisted_44 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "B2C", -1
/* HOISTED */
);

var _hoisted_45 = {
  "class": "col-4 float-right width-fit-content"
};
var _hoisted_46 = {
  "class": "col-sm-4"
};
var _hoisted_47 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};
var _hoisted_48 = {
  "class": "row body_bold"
};

var _hoisted_49 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8"
}, "Total Items (by service)", -1
/* HOISTED */
);

var _hoisted_50 = {
  "class": "col-2 float-right"
};
var _hoisted_51 = {
  "class": "col-12"
};

var _hoisted_52 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row chart-title-row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-6 text-align-right"
}, "Services"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-6 text-align-center pl-0"
}, "Today")], -1
/* HOISTED */
);

var _hoisted_53 = {
  "class": "col-6 text-right each-dept-name pl-0"
};
var _hoisted_54 = {
  "class": "col-6"
};
var _hoisted_55 = {
  "class": "row"
};
var _hoisted_56 = {
  "class": "col-sm-4"
};
var _hoisted_57 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};
var _hoisted_58 = {
  "class": "row fw-bold"
};

var _hoisted_59 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8 p-0 text-nowrap"
}, "Sales from 1st time Customers", -1
/* HOISTED */
);

var _hoisted_60 = {
  "class": "col-4 float-right"
};
var _hoisted_61 = {
  "class": "row"
};

var _hoisted_62 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row fw-italic"
}, "Stores", -1
/* HOISTED */
);

var _hoisted_63 = {
  "class": "row text-center"
};
var _hoisted_64 = {
  "class": "first-bar col-3"
};
var _hoisted_65 = {
  "class": "second-bar col-3"
};
var _hoisted_66 = {
  "class": "third-bar col-3"
};
var _hoisted_67 = {
  "class": "fourth-bar col-3"
};

var _hoisted_68 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row text-center"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "MB"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "NH"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "CH"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3"
}, "SK")], -1
/* HOISTED */
);

var _hoisted_69 = {
  "class": "row"
};

var _hoisted_70 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row fw-italic"
}, "Deliveries", -1
/* HOISTED */
);

var _hoisted_71 = {
  "class": "row"
};
var _hoisted_72 = {
  "class": "first-bar col-4"
};
var _hoisted_73 = {
  "class": "second-bar col-4"
};
var _hoisted_74 = {
  "class": "fourth-bar col-4"
};

var _hoisted_75 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row text-center"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-4"
}, "APP"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-4"
}, "CC"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-4"
}, "B2B")], -1
/* HOISTED */
);

var _hoisted_76 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};

var _hoisted_77 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row body_bold"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8"
}, "New Sign Ups"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <div class=\"col-2 float-right\">£200</div>\r\n                                        <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")], -1
/* HOISTED */
);

var _hoisted_78 = {
  "class": "row"
};
var _hoisted_79 = {
  "class": "row"
};

var _hoisted_80 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Stores", -1
/* HOISTED */
);

var _hoisted_81 = {
  "class": "col-2 float-right width-fit-content"
};
var _hoisted_82 = {
  "class": "row"
};

var _hoisted_83 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Delivery", -1
/* HOISTED */
);

var _hoisted_84 = {
  "class": "col-2 float-right width-fit-content"
};
var _hoisted_85 = {
  "class": "row"
};

var _hoisted_86 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "B2B", -1
/* HOISTED */
);

var _hoisted_87 = {
  "class": "col-2 float-right width-fit-content"
};
var _hoisted_88 = {
  "class": "row"
};

var _hoisted_89 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "B2C", -1
/* HOISTED */
);

var _hoisted_90 = {
  "class": "col-2 float-right width-fit-content"
};
var _hoisted_91 = {
  "class": "container-stat position-relative mt-10"
};
var _hoisted_92 = {
  "class": "row pb4"
};
var _hoisted_93 = {
  "class": "col-sm-8"
};
var _hoisted_94 = {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
};
var _hoisted_95 = {
  "class": "row body_bold"
};

var _hoisted_96 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8"
}, "Total Items (by service)", -1
/* HOISTED */
);

var _hoisted_97 = {
  "class": "col-2 float-right"
};
var _hoisted_98 = {
  "class": "col-12"
};

var _hoisted_99 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row chart-title-row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-2"
}), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-4 pl-0"
}, "TOTAL"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-2 text-align-center pl-0"
}, "Stores"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-2 text-align-center pl-0"
}, "App"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-2 text-align-center pl-0"
}, "B2B")], -1
/* HOISTED */
);

var _hoisted_100 = {
  "class": "col-2 text-right each-dept-name pl-0"
};

var _hoisted_101 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-4"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <div\r\n                                                        v-if=\"a > 0\"\r\n                                                        class=\"chart-bar\"\r\n                                                        :style=\"{\r\n                                                            width: parseInt(a) * parseFloat(width_scale) +'%',\r\n                                                            background: top_3_today[i]? top_3_today[i]: '#e7e7e7',\r\n                                                        }\"\r\n                                                    ></div> "), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <div\r\n                                                        class=\"col-2 today-count\"\r\n                                                        :class=\"{' pl-0': group.total === 0,}\"\r\n                                                    >{{  }}</div> ")])], -1
/* HOISTED */
);

var _hoisted_102 = {
  "class": "col-2 text-align-center each-dept-name pl-0"
};
var _hoisted_103 = {
  "class": "col-2 text-align-center each-dept-name pl-0"
};
var _hoisted_104 = {
  "class": "col-2 text-align-center each-dept-name pl-0"
};

var _hoisted_105 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-sm-4 d-none"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "container-fluid col-12 bg-white py-4 mb-3"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row body_bold"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-8"
}, "Key figures")]), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "YoY sales Growth"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3 float-right width-fit-content"
}, "22"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "CLV"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3 float-right width-fit-content"
}, "£136"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "CAC"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3 float-right width-fit-content"
}, "£10"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Total cust."), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3 float-right width-fit-content"
}, "23,000"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "row"
}, [/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-5"
}, "Total active cust."), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
  "class": "col-3 float-right width-fit-content"
}, "15,000"), /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")])])])], -1
/* HOISTED */
);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_main_header = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("main-header");

  var _component_side_bar = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("side-bar");

  var _component_statisticsFilters = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("statisticsFilters");

  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
    "enter-active-class": "animate__animated animate__fadeIn"
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_main_header), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_side_bar), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_3, [_hoisted_4, _hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_statisticsFilters, {
        filterVal: $setup.filterVal,
        "onUpdate:filterVal": _cache[1] || (_cache[1] = function (newValue) {
          return $setup.filterVal = newValue;
        })
      }, null, 8
      /* PROPS */
      , ["filterVal"])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_12, [_hoisted_13, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_14, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.total_sales), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_15, [_hoisted_16, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_17, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_18, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.mb), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_19, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.nh), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_20, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.ch), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_21, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.sk), 1
      /* TEXT */
      )]), _hoisted_22]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_23, [_hoisted_24, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_25, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_26, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.b2c), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_27, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.b2b), 1
      /* TEXT */
      )]), _hoisted_28])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_29, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_30, [_hoisted_31, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_32, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.avg_total_order), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_33, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_34, [_hoisted_35, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_36, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.avg_store_order), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_37, [_hoisted_38, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_39, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.avg_delivery_order), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_40, [_hoisted_41, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_42, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.avg_b2b_order), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_43, [_hoisted_44, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_45, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.avg_b2c_order), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_46, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_47, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_48, [_hoisted_49, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_50, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.service_items_total), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_51, [_hoisted_52, ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.statistique.service_items, function (item, i) {
        return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
          "class": "row chart-row align-items-center",
          id: "stats_today",
          key: i
        }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_53, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(item.DepartmentName), 1
        /* TEXT */
        ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_54, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_55, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <div\r\n                                                        v-if=\"item.total > 0\"\r\n                                                        class=\"chart-bar\"\r\n                                                        :style=\"{\r\n                                                            width: parseInt(item.total) * 0.01 + '%',\r\n                                                            background: '#e7e7e7',\r\n                                                        }\"\r\n                                                    ></div> "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", {
          "class": ["w-100 today-count text-center", {
            ' pl-0': item.total === 0
          }]
        }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(item.total.toFixed(2)), 3
        /* TEXT, CLASS */
        )])])]);
      }), 128
      /* KEYED_FRAGMENT */
      ))])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_56, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_57, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_58, [_hoisted_59, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_60, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_total_sales), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_61, [_hoisted_62, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_63, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_64, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_mb), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_65, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_nh), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_66, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_ch), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_67, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_sk), 1
      /* TEXT */
      )]), _hoisted_68]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_69, [_hoisted_70, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_71, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_72, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_app), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_73, "£200" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_normal), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_74, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.first_time_b2b), 1
      /* TEXT */
      )]), _hoisted_75])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_76, [_hoisted_77, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_78, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_79, [_hoisted_80, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_81, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.stores_new_signup), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_82, [_hoisted_83, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_84, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.delivery_new_signup), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_85, [_hoisted_86, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_87, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.b2b_new_signup), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_88, [_hoisted_89, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_90, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.b2c_new_signup), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-down red px-0 width-auto\"></i>\r\n                                            <div class=\"col-2 px-0 float-right\">50%</div> ")])])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_91, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_92, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_93, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_94, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_95, [_hoisted_96, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_97, "£" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.statistique.services_group_total), 1
      /* TEXT */
      ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <i class=\"col-1 bi bi-arrow-up green px-0 width-auto\"></i>\r\n                                        <div class=\"col-1 px-0 float-right\">50%</div> ")]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_98, [_hoisted_99, ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.statistique.services_group, function (group, i) {
        return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)("div", {
          "class": "row chart-row align-items-center",
          id: "stats_today",
          key: i
        }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_100, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(group.DepartmentName), 1
        /* TEXT */
        ), _hoisted_101, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_102, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(group.total.toFixed(2)), 1
        /* TEXT */
        ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_103, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(group.total.toFixed(2)), 1
        /* TEXT */
        ), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)("div", _hoisted_104, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(group.total.toFixed(2)), 1
        /* TEXT */
        )]);
      }), 128
      /* KEYED_FRAGMENT */
      ))])])]), _hoisted_105])])])])])];
    }),
    _: 1
    /* STABLE */

  });
}

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../node_modules/css-loader/dist/runtime/getUrl.js */ "./node_modules/css-loader/dist/runtime/getUrl.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _img_calendar_svg__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../img/calendar.svg */ "./resources/img/calendar.svg");
/* harmony import */ var _img_chevron_svg__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../img/chevron.svg */ "./resources/img/chevron.svg");
/* harmony import */ var _img_chevron_w_svg__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../img/chevron_w.svg */ "./resources/img/chevron_w.svg");
// Imports





var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
var ___CSS_LOADER_URL_REPLACEMENT_0___ = _node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1___default()(_img_calendar_svg__WEBPACK_IMPORTED_MODULE_2__.default);
var ___CSS_LOADER_URL_REPLACEMENT_1___ = _node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1___default()(_img_chevron_svg__WEBPACK_IMPORTED_MODULE_3__.default);
var ___CSS_LOADER_URL_REPLACEMENT_2___ = _node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_1___default()(_img_chevron_w_svg__WEBPACK_IMPORTED_MODULE_4__.default);
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.mw-picker[data-v-6cc0b0f0]{\n        width: calc(100% - 48px);\n}\ninput[data-v-6cc0b0f0]{\n        border: 1px solid #000000;\n        box-sizing: border-box;\n        border-radius: 5px;\n        background: transparent url(" + ___CSS_LOADER_URL_REPLACEMENT_0___ + ") no-repeat center right 10px;\n        background-size: 12px;\n        width: 154px;\n        height: 40px;\n        line-height: 40px;\n        padding-left: 22px;\n        color: #000000;\n        vertical-align: middle;\n        font-size: 18px;\n        padding-right: 30px;\n}\n.btn-dp[data-v-6cc0b0f0]{\n        width: 44px;\n        height: 44px;\n        position: relative;\n        background: #FFFFFF url(" + ___CSS_LOADER_URL_REPLACEMENT_1___ + ") no-repeat center center;\n        background-size: 8px;\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.2);\n        border-radius: 80px;\n        transition: all 0.2s ease-in-out;\n}\n.btn-dp.minus[data-v-6cc0b0f0]{\n        transform: rotate(180deg);\n        box-shadow: 0px -1px 1px rgba(0, 14, 51, 0.2);\n}\n.btn-dp[data-v-6cc0b0f0]:hover{\n\n        color: #fff;\n        background:#47454B url(" + ___CSS_LOADER_URL_REPLACEMENT_2___ + ") no-repeat center center;\n        background-size: 8px;\n}\n.dp[data-v-6cc0b0f0]{\n        position: relative;\n        width: 154px;\n}\n.dp-picker[data-v-6cc0b0f0]{\n        position: absolute;\n        width: 426px;\n        min-height: 396px;\n        background: #F5F5F5;\n        box-shadow: 0px 1px 2px rgba(0, 14, 51, 0.25);\n        border-radius: 16px;\n        z-index: 100;\n        padding: 24px;\n        transform-origin: top left;\n        transition: min-height 0.3s ease-in-out;\n}\n.dp-picker.row6[data-v-6cc0b0f0]{\n        min-height: 426px;\n}\n.dp-dayname[data-v-6cc0b0f0]{\n        font-weight: 500;\n        text-align: center;\n        max-width: 50px;\n        height: 44px;\n        line-height: 44px;\n}\n.dp-dates[data-v-6cc0b0f0]{\n        text-align: center;\n        padding: 0;\n        max-width: 50px;\n        height: 44px;\n        background: #FFFFFF;\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);\n        border-radius: 6px;\n        line-height: 44px;\n        font-weight: 700;\n        cursor: pointer;\n        transition: all 0.2s ease-in-out;\n}\n.dp-dates.disabled[data-v-6cc0b0f0]{\n        pointer-events: none;\n        background: transparent;\n        color:rgba(0, 23, 84, 0.15);\n        box-shadow: none;\n        cursor: default;\n}\n.dp-dates[data-v-6cc0b0f0]:not(.disabled):not(.current):hover{\n        background-color: #47454B;\n        color: #fff;\n}\n.dp-dates.current[data-v-6cc0b0f0]{\n        background-color: #0047FF;\n        color: #FFF;\n}\n.dp-dates.disabled.current[data-v-6cc0b0f0]{\n        opacity: 0.5;\n}\n.dp-months[data-v-6cc0b0f0]{\n        text-align: center;\n        padding: 0;\n        max-width: 50px;\n        height: 44px;\n        background: #FFFFFF;\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);\n        border-radius: 6px;\n        line-height: 44px;\n        font-weight: 800;\n        font-family: Gilroy;\n        cursor: pointer;\n        transition: all 0.2s ease-in-out;\n}\n.dp-months.disabled[data-v-6cc0b0f0]{\n        pointer-events: none;\n        background: transparent;\n        color:rgba(0, 23, 84, 0.15);\n        box-shadow: none;\n        cursor: default;\n}\n.dp-months[data-v-6cc0b0f0]:not(.disabled):not(.current):hover{\n        background-color: #47454B;\n        color: #fff;\n}\n.dp-months.current[data-v-6cc0b0f0]{\n        background-color: #0047FF;\n        color: #FFF;\n}\n.dp-months.disabled.current[data-v-6cc0b0f0]{\n        opacity: 0.5;\n}\n.dp-years[data-v-6cc0b0f0]{\n        text-align: center;\n        padding: 0;\n        max-width: 50px;\n        height: 44px;\n        background: #FFFFFF;\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);\n        border-radius: 6px;\n        line-height: 44px;\n        font-weight: 700;\n        cursor: pointer;\n        transition: all 0.2s ease-in-out;\n}\n.dp-years.disabled[data-v-6cc0b0f0]{\n        pointer-events: none;\n        background: transparent;\n        color:rgba(0, 23, 84, 0.15);\n        box-shadow: none;\n        cursor: default;\n}\n.dp-years[data-v-6cc0b0f0]:not(.disabled):not(.current):hover{\n        background-color: #47454B;\n        color: #fff;\n}\n.dp-years.current[data-v-6cc0b0f0]{\n        background-color: #0047FF;\n        color: #FFF;\n}\n.dp-years.disabled.current[data-v-6cc0b0f0]{\n        opacity: 0.5;\n}\n.dp .row[data-v-6cc0b0f0]{\n        justify-content: space-evenly;\n        margin-bottom: 4px;\n        font-size: 16px;\n        align-items: center;\n}\n.dp .row-months[data-v-6cc0b0f0]{\n        margin-bottom: 20px;\n}\n.dp .row-years[data-v-6cc0b0f0]{\n        margin-bottom: 40px;\n}\n.btn-month[data-v-6cc0b0f0],.btn-year[data-v-6cc0b0f0]{\n        background: #FFFFFF;\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);\n        border-radius: 6px;\n        height: 44px;\n        font-weight: 800;\n        font-family: Gilroy;\n        vertical-align: middle;\n        font-size: 22px;\n        position: relative;\n        transition: all 0.2s ease-in-out;\n        padding: 7px 22px 7px 14px;\n}\n.btn-month[data-v-6cc0b0f0]{\n        margin-right: 2px;\n}\n.btn-month[data-v-6cc0b0f0]:hover,.btn-year[data-v-6cc0b0f0]:hover{\n        background-color: #47454B;\n        color: #fff;\n}\n.btn-month[data-v-6cc0b0f0]:after,.btn-year[data-v-6cc0b0f0]:after{\n        content:\"\";\n        width:8px;\n        height: 8px;\n        border: 4px solid transparent;\n        border-right-color: #0047FF;\n        border-bottom-color: #0047FF;\n        border-radius: 3px;\n        top:24px;\n        right: 11px;\n        position: absolute;\n        transition: all 0.2s ease-in-out;\n}\n.btn-month[data-v-6cc0b0f0]:hover:after,.btn-year[data-v-6cc0b0f0]:hover:after{\n        border-right-color: #FFF;\n        border-bottom-color: #FFF;\n}\n.trans-dp-picker-enter-from[data-v-6cc0b0f0]{\n        opacity: 0;\n        transform: scale(0.6);\n}\n.trans-dp-picker-enter-to[data-v-6cc0b0f0]{\n        opacity: 1;\n        transform: scale(1);\n}\n.trans-dp-picker-enter-active[data-v-6cc0b0f0]{\n        transition: all ease 0.2s;\n}\n.trans-dp-picker-leave-from[data-v-6cc0b0f0]{\n        opacity: 1;\n        transform: scale(1);\n}\n.trans-dp-picker-leave-to[data-v-6cc0b0f0]{\n        opacity: 0;\n        transform: scale(0.6);\n}\n.trans-dp-picker-leave-active[data-v-6cc0b0f0]{\n        transition: all ease 0.2s;\n}\n.trans-dp-picker-zoom-enter-from[data-v-6cc0b0f0]{\n        opacity: 0;\n        transform: scale(0.6);\n}\n.trans-dp-picker-zoom-enter-to[data-v-6cc0b0f0]{\n        opacity: 1;\n        transform: scale(1);\n}\n.trans-dp-picker-zoom-enter-active[data-v-6cc0b0f0]{\n        transition: all ease 0.2s;\n}\n.trans-dp-picker-zoom-leave-from[data-v-6cc0b0f0]{\n        opacity: 1;\n        transform: scale(1);\n}\n.trans-dp-picker-zoom-leave-to[data-v-6cc0b0f0]{\n        opacity: 0;\n        transform: scale(0.6);\n}\n.trans-dp-picker-zoom-leave-active[data-v-6cc0b0f0]{\n        transition: all ease 0.2s;\n}\n.hint[data-v-6cc0b0f0]{\n        margin-bottom: 20px;\n        font-size: 16px;\n        color: #757575;;\n        font-weight: 300;\n}\n.notavailable[data-v-6cc0b0f0]{\n        color: #EB5757;\n        background: rgba(245, 171, 171, 0.7);\n        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);\n        pointer-events: none;\n}\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.trans-select-enter-from[data-v-31289606]{\r\n        opacity: 0;\r\n        transform: scale(0.6);\n}\n.trans-select-enter-to[data-v-31289606]{\r\n        opacity: 1;\r\n        transform: scale(1);\n}\n.trans-select-enter-active[data-v-31289606]{\r\n        transition: all ease 0.2s;\n}\n.trans-select-leave-from[data-v-31289606]{\r\n        opacity: 1;\r\n        transform: scale(1);\n}\n.trans-select-leave-to[data-v-31289606]{\r\n        opacity: 0;\r\n        transform: scale(0.6);\n}\n.trans-select-leave-active[data-v-31289606]{\r\n        transition: all ease 0.2s;\n}\n.black-border[data-v-31289606]{\r\n        border: 1px solid #000000 !important;\n}\n.select[data-v-31289606]{\r\n        background: #FFFFFF;\r\n        border: 0.5px solid #E0E0E0;\r\n        box-sizing: border-box;\r\n        border-radius: 5px;\r\n        padding: 0 36px 0 16px;\r\n        height: 40px;\r\n        font-size: 14px;\r\n        display: flex;\r\n        cursor: pointer;\r\n        align-items: center;\r\n        position: relative;\n}\n.select.active[data-v-31289606]{\r\n\r\n        background: #EEEEEE;\r\n        border: 1px solid #EEEEEE;\r\n        outline: 2px #000000 solid;\n}\n.select.active .disp[data-v-31289606]{\r\n        font-weight: 600;\n}\n.select-options[data-v-31289606]{\r\n        position: absolute;\r\n        width: 100%;\r\n        left: 0;\r\n        top: 44px;\r\n        background: #FFF;\r\n        box-shadow: inset 0px 0px 4px rgba(37, 40, 43, 0.12);\r\n        max-height: 168px;\r\n        z-index: 1;\r\n        overflow-y: auto;\r\n        transform-origin: top center;\n}\n.select[data-v-31289606]:after,.select[data-v-31289606]:before{\r\n        content: \" \";\r\n        height: 3px;\r\n        display: block;\r\n        width: 13px;\r\n        background: #868686;\r\n        border-radius: 10px;\r\n        transform: rotate(40deg);\r\n        right:22px;\r\n        position: absolute;\n}\n.select.active[data-v-31289606]:after,.select.active[data-v-31289606]:before{\r\n        background: #000000;\n}\n.select[data-v-31289606]:after{\r\n        transform: rotate(-40deg);\r\n        right: 13px;\n}\n.opts[data-v-31289606]{\r\n        height: 56px;\r\n        padding: 17px 0 17px 16px;\r\n        font-size: 16px;\n}\n.opts[data-v-31289606]:hover{\r\n        background: #EEEEEE;\n}\n.hint ~ .select[data-v-31289606]{\r\n        margin-bottom: 20px;\n}\n.hint[data-v-31289606]{\r\n        margin-bottom: 20px;\r\n        font-size: 16px;\r\n        color: #757575;;\r\n        font-weight: 300;\n}\n.select:not(.active) .placeholder[data-v-31289606]{\r\n        color:#868686;\n}\n.select.selected[data-v-31289606]{\r\n       background: #F8F8F8;\r\n        font-size: 16px;\n}\n.select[data-v-31289606]:active{\r\n        background-color: #E0E0E0;\r\n        border-color: #E0E0E0;\n}\n.disabled[data-v-31289606]{\r\n        pointer-events: none;\n}\n.select-label.disabled[data-v-31289606]{\r\n        color:#C3C3C3;\n}\n.select.valid[data-v-31289606]{\r\n        background-color: #F7FBF6;\n}\n.select.valid .disp[data-v-31289606]{\r\n        color: #42A71E;\n}\n.select.valid[data-v-31289606]:before,.select.valid[data-v-31289606]:after{\r\n        background-color: #42A71E;\n}\n.select.invalid[data-v-31289606]{\r\n        background-color: #FFEFED;\n}\n.select.invalid .disp[data-v-31289606]{\r\n        color: #EB5757;\n}\n.select.invalid[data-v-31289606]:before,.select.invalid[data-v-31289606]:after{\r\n        background-color: #EB5757;\n}\n.select.disabled[data-v-31289606]{\r\n        background-color: #F8F8F8;\n}\n.select.disabled .disp[data-v-31289606]{\r\n        color:#C3C3C3;\n}\n.select.disabled[data-v-31289606]:before, .select.disabled[data-v-31289606]:after{\r\n        background-color: #C3C3C3;\n}\r\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.text-right[data-v-5c5adc5c]{\r\n        text-align: right;\n}\n.filters .buttons button[data-v-5c5adc5c]{\r\n        width: 130px;\r\n        height: 56px;\n}\n.filters .buttons[data-v-5c5adc5c]{\r\n        margin-top: 51px;\r\n        margin-bottom: 51px;\n}\n.filter[data-v-5c5adc5c]{\r\n        padding: 10px 16px;\n}\n.filter.hasfilters[data-v-5c5adc5c]:not(:hover){\r\n        background:#42A71E ;\r\n        color:#FFF;\r\n        border-color: #42A71E;\n}\n.filter.hasfilters.active[data-v-5c5adc5c]{\r\n        background: #47454B;\r\n        border-color: #47454B;\n}\n.filter[data-v-5c5adc5c]:focus{\r\n        box-shadow: none;\n}\n.filter.hasfilters:not(:hover) span[data-v-5c5adc5c],.filter.hasfilters:not(:hover) span[data-v-5c5adc5c]:before,.filter.hasfilters:not(:hover) span[data-v-5c5adc5c]:after{\r\n        background: #FFF;\n}\n.filter:hover span[data-v-5c5adc5c], .filter:hover span[data-v-5c5adc5c]:before, .filter:hover span[data-v-5c5adc5c]:after,\r\n    .filter.active span[data-v-5c5adc5c], .filter.active span[data-v-5c5adc5c]:before, .filter.active span[data-v-5c5adc5c]:after\r\n    {\r\n        background: #FFF;\n}\n.filters[data-v-5c5adc5c]{\r\n        background: #F8F8F8;\r\n        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.12);\r\n        width:444px;\r\n        min-height:250px;\r\n        z-index: 2;\r\n        transform-origin: top center;\r\n        padding:0 46px;\n}\n.bi.bi-calendar[data-v-5c5adc5c]{\r\n        font-size: 20px;\n}\n.filters h2[data-v-5c5adc5c]{\r\n        margin:30px 0 20px 0;\n}\n.trans-filter-enter-from[data-v-5c5adc5c]{\r\n        opacity: 0;\r\n        transform: scale(0.6);\n}\n.trans-filter-enter-to[data-v-5c5adc5c]{\r\n        opacity: 1;\r\n        transform: scale(1);\n}\n.trans-filter-enter-active[data-v-5c5adc5c]{\r\n        transition: all ease 0.2s;\n}\n.trans-filter-leave-from[data-v-5c5adc5c]{\r\n        opacity: 1;\r\n        transform: scale(1);\n}\n.trans-filter-leave-to[data-v-5c5adc5c]{\r\n        opacity: 0;\r\n        transform: scale(0.6);\n}\n.trans-filter-leave-active[data-v-5c5adc5c]{\r\n        transition: all ease 0.2s;\n}\n.select[data-v-5c5adc5c]{\r\n        background: #FFFFFF;\r\n        border: 1px solid #000000;;\r\n        box-sizing: border-box;\r\n        border-radius: 5px;\r\n        padding: 0 36px 0 16px;\r\n        height: 40px;\r\n        font-size: 16px;\r\n        display: flex;\r\n        cursor: pointer;\r\n        align-items: center;\r\n        position: relative;\r\n        margin-bottom: 30px;\n}\n.select.active[data-v-5c5adc5c]{\r\n        margin-right: -2px;\r\n        margin-left: -2px;\r\n        background: #EEEEEE;\r\n        border: 2px solid #000000;\n}\n.select-options[data-v-5c5adc5c]{\r\n        position: absolute;\r\n        width: 100%;\r\n        left: 0;\r\n        top: 46px;\r\n        background: #FFF;\r\n        box-shadow: inset 0px 0px 4px rgba(37, 40, 43, 0.12);\r\n        max-height: 168px;\r\n        z-index: 1;\r\n        overflow-y: auto;\r\n        transform-origin: top center;\n}\n.select[data-v-5c5adc5c]:after,.select[data-v-5c5adc5c]:before{\r\n        content: \" \";\r\n        height: 3px;\r\n        display: block;\r\n        width: 13px;\r\n        background: #868686;\r\n        border-radius: 10px;\r\n        transform: rotate(40deg);\r\n        right:22px;\r\n        position: absolute;\n}\n.select.active[data-v-5c5adc5c]:after,.select.active[data-v-5c5adc5c]:before{\r\n    background: #000000;\n}\n.select[data-v-5c5adc5c]:after{\r\n        transform: rotate(-40deg);\r\n        right: 13px;\n}\n.custom-filter-text[data-v-5c5adc5c]{\r\n        margin-top: 12px;\r\n        font-size: 14px;\n}\r\n\r\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.switch-wrapper[data-v-c68a14e8]{\r\n        display: flex;\r\n        align-items: center;\n}\n.switch-wrapper label[data-v-c68a14e8]{\r\n        cursor: pointer;\n}\n.switch[data-v-c68a14e8]{\r\n        background-color: #C3C3C3;\r\n        height: 28px;\r\n        padding: 5px 3px 5px 8px;\r\n        border-radius: 40px;\r\n        display: flex;\r\n        align-items: center;\r\n        width: 65px;\r\n        position: relative;\r\n        cursor: pointer;\r\n        transition: 0.3s ease-in-out background-color;\n}\n.switch span[data-v-c68a14e8]{\r\n        color:#FFF;\n}\n.switch[data-v-c68a14e8]:before{\r\n        content: \" \";\r\n        width: 18px;\r\n        height: 18px;\r\n        position: absolute;\r\n        background: #FFF;\r\n        top:5px;\r\n        left:3px;\r\n        border-radius: 50%;\r\n        transition: 0.3s ease-in-out left;\n}\n.switch span[data-v-c68a14e8]{\r\n        transition: 0.2s ease-in-out opacity;\n}\n.switch.on[data-v-c68a14e8]{\r\n        background-color: #42A71E;\n}\n.switch.on[data-v-c68a14e8]:before{\r\n        left: 44px;\n}\n.switch span[data-v-c68a14e8]:first-child{\r\n        opacity: 0;\n}\n.switch span[data-v-c68a14e8]:last-child{\r\n        opacity: 1;\n}\n.switch.on span[data-v-c68a14e8]:last-child{\r\n        opacity: 0;\n}\n.switch.on span[data-v-c68a14e8]:first-child{\r\n        opacity: 1;\n}\n.disabled .switch[data-v-c68a14e8]{\r\n        opacity: 0.5;\r\n        cursor: default;\r\n        pointer-events: none;\n}\n.disabled label[data-v-c68a14e8]{\r\n        opacity: 0.5;\r\n        pointer-events: none;\r\n        cursor: default;\n}\n.switch-wrapper label[data-v-c68a14e8]:first-child{\r\n        margin-right: 8px;\n}\n.switch-wrapper label[data-v-c68a14e8]:last-child{\r\n        margin-left: 8px;\n}\r\n\r\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_style_index_0_id_6cc0b0f0_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_style_index_0_id_6cc0b0f0_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default, options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_style_index_0_id_6cc0b0f0_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default.locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_style_index_0_id_31289606_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_style_index_0_id_31289606_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default, options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_style_index_0_id_31289606_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default.locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_style_index_0_id_5c5adc5c_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_style_index_0_id_5c5adc5c_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default, options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_style_index_0_id_5c5adc5c_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default.locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_style_index_0_id_c68a14e8_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_style_index_0_id_c68a14e8_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default, options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_style_index_0_id_c68a14e8_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_1__.default.locals || {});

/***/ }),

/***/ "./resources/js/components/miscellaneous/DatePicker.vue":
/*!**************************************************************!*\
  !*** ./resources/js/components/miscellaneous/DatePicker.vue ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _DatePicker_vue_vue_type_template_id_6cc0b0f0_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true */ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true");
/* harmony import */ var _DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./DatePicker.vue?vue&type=script&lang=js */ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js");
/* harmony import */ var _DatePicker_vue_vue_type_style_index_0_id_6cc0b0f0_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css */ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css");




;
_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.render = _DatePicker_vue_vue_type_template_id_6cc0b0f0_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render
_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__scopeId = "data-v-6cc0b0f0"
/* hot reload */
if (false) {}

_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__file = "resources/js/components/miscellaneous/DatePicker.vue"

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default);

/***/ }),

/***/ "./resources/js/components/miscellaneous/SelectOptions.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SelectOptions.vue ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _SelectOptions_vue_vue_type_template_id_31289606_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SelectOptions.vue?vue&type=template&id=31289606&scoped=true */ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true");
/* harmony import */ var _SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SelectOptions.vue?vue&type=script&lang=js */ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js");
/* harmony import */ var _SelectOptions_vue_vue_type_style_index_0_id_31289606_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css */ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css");




;
_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.render = _SelectOptions_vue_vue_type_template_id_31289606_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render
_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__scopeId = "data-v-31289606"
/* hot reload */
if (false) {}

_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__file = "resources/js/components/miscellaneous/SelectOptions.vue"

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default);

/***/ }),

/***/ "./resources/js/components/miscellaneous/StatisticsFilters.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/components/miscellaneous/StatisticsFilters.vue ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _StatisticsFilters_vue_vue_type_template_id_5c5adc5c_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true */ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true");
/* harmony import */ var _StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./StatisticsFilters.vue?vue&type=script&lang=js */ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js");
/* harmony import */ var _StatisticsFilters_vue_vue_type_style_index_0_id_5c5adc5c_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css */ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css");




;
_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.render = _StatisticsFilters_vue_vue_type_template_id_5c5adc5c_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render
_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__scopeId = "data-v-5c5adc5c"
/* hot reload */
if (false) {}

_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__file = "resources/js/components/miscellaneous/StatisticsFilters.vue"

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default);

/***/ }),

/***/ "./resources/js/components/miscellaneous/SwitchBtn.vue":
/*!*************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SwitchBtn.vue ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _SwitchBtn_vue_vue_type_template_id_c68a14e8_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true */ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true");
/* harmony import */ var _SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SwitchBtn.vue?vue&type=script&lang=js */ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js");
/* harmony import */ var _SwitchBtn_vue_vue_type_style_index_0_id_c68a14e8_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css */ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css");




;
_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.render = _SwitchBtn_vue_vue_type_template_id_c68a14e8_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render
_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__scopeId = "data-v-c68a14e8"
/* hot reload */
if (false) {}

_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__file = "resources/js/components/miscellaneous/SwitchBtn.vue"

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default);

/***/ }),

/***/ "./resources/js/components/statistics/Statistics.vue":
/*!***********************************************************!*\
  !*** ./resources/js/components/statistics/Statistics.vue ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Statistics_vue_vue_type_template_id_6f1cce18__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Statistics.vue?vue&type=template&id=6f1cce18 */ "./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18");
/* harmony import */ var _Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Statistics.vue?vue&type=script&lang=js */ "./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js");



_Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.render = _Statistics_vue_vue_type_template_id_6f1cce18__WEBPACK_IMPORTED_MODULE_0__.render
/* hot reload */
if (false) {}

_Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default.__file = "resources/js/components/statistics/Statistics.vue"

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__.default);

/***/ }),

/***/ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js":
/*!**************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__.default)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./DatePicker.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__.default)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SelectOptions.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js":
/*!*********************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__.default)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./StatisticsFilters.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js":
/*!*************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__.default)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SwitchBtn.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js":
/*!***********************************************************************************!*\
  !*** ./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__.default)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Statistics_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Statistics.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true":
/*!********************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true ***!
  \********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_template_id_6cc0b0f0_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_template_id_6cc0b0f0_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=template&id=6cc0b0f0&scoped=true");


/***/ }),

/***/ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true":
/*!***********************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true ***!
  \***********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_template_id_31289606_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_template_id_31289606_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SelectOptions.vue?vue&type=template&id=31289606&scoped=true */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=template&id=31289606&scoped=true");


/***/ }),

/***/ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true":
/*!***************************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true ***!
  \***************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_template_id_5c5adc5c_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_template_id_5c5adc5c_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=template&id=5c5adc5c&scoped=true");


/***/ }),

/***/ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true":
/*!*******************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true ***!
  \*******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_template_id_c68a14e8_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_template_id_c68a14e8_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=template&id=c68a14e8&scoped=true");


/***/ }),

/***/ "./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18 ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Statistics_vue_vue_type_template_id_6f1cce18__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Statistics_vue_vue_type_template_id_6f1cce18__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Statistics.vue?vue&type=template&id=6f1cce18 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/statistics/Statistics.vue?vue&type=template&id=6f1cce18");


/***/ }),

/***/ "./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css":
/*!**********************************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css ***!
  \**********************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_DatePicker_vue_vue_type_style_index_0_id_6cc0b0f0_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader/dist/cjs.js!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/DatePicker.vue?vue&type=style&index=0&id=6cc0b0f0&scoped=true&lang=css");


/***/ }),

/***/ "./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css":
/*!*************************************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css ***!
  \*************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SelectOptions_vue_vue_type_style_index_0_id_31289606_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader/dist/cjs.js!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SelectOptions.vue?vue&type=style&index=0&id=31289606&scoped=true&lang=css");


/***/ }),

/***/ "./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css":
/*!*****************************************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css ***!
  \*****************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_StatisticsFilters_vue_vue_type_style_index_0_id_5c5adc5c_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader/dist/cjs.js!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/StatisticsFilters.vue?vue&type=style&index=0&id=5c5adc5c&scoped=true&lang=css");


/***/ }),

/***/ "./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css":
/*!*********************************************************************************************************************!*\
  !*** ./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css ***!
  \*********************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_SwitchBtn_vue_vue_type_style_index_0_id_c68a14e8_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader/dist/cjs.js!../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/components/miscellaneous/SwitchBtn.vue?vue&type=style&index=0&id=c68a14e8&scoped=true&lang=css");


/***/ })

}]);