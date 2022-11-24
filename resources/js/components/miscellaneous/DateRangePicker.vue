<template>
    <div class="dp noselect">
        <label class="select-label" :class="{disabled:disabled==true}" v-if="label">{{ label }}</label>
        <input type="text" readonly :placeholder="placeholder" class="w-100 bg-white date-picker" :style="{'color': color, 'font-size': font}" v-model="formated_date" @click="toggleshowDp"/>
        <transition name="trans-dp-picker" >
            <div class="dp-picker" id="dateRangePicker" v-if="sel===name" :class="{row6:displayed_dates_rows[5].length>0&&currentView=='dates' }" :style="{top:droppos.top,right:droppos.right,bottom:droppos.bottom,left:droppos.left,transformOrigin:droppos.transformOrigin}">
                <div class="row" id="dateRangePickerHeader">
                    <div class="col-2"><button @click="minusMonth" class="btn btn-dp minus"></button></div>
                    <div class="col-8 text-center"><button class="btn btn-month" @click="showMonths">{{monthsName[MonthYear.month].name}}</button><button class="btn btn-year" @click="showYears">{{MonthYear.year}}</button></div>
                    <div class="col-2"><button @click="plusMonth" class="btn btn-dp "></button></div>
                </div>
                <transition name="trans-dp-picker-zoom" >
                    <div v-if="currentView=='dates'" class="position-absolute mw-picker" id="dateRangePickerBody">
                        <div class="row">
                            <div class="col dp-dayname" v-for="day in days" :key="day">
                                {{ day.dayName }}
                            </div>
                        </div>
                        <div class="row" v-for="row in displayed_dates_rows" :key="row">
                            <template v-for="(day,index) in row" :key="index">
                                <div class="col dp-dates" :class="{ disabled:!day.current_month, start: day.start, current:day.end, notavailable:day.notavailable, period: day.period}" @click="setDate(day.year,day.month,day.date)" >
                                    {{day.date}}
                                </div>
                            </template>
                        </div>
                        <button class="btn-sm btn-secondary reset-daterange mt-3" @click="resetFilter">Clear</button>
                    </div>
                </transition>
                <transition name="trans-dp-picker-zoom" class="position-absolute mw-picker" id="dateRangePickerBody">
                <div v-if="currentView=='months'">
                    <div class="row row-months" v-for="(row,i) in displayed_months_rows" :key="i" :class="{'mt-5':i==0}">
                        <template v-for="(month,index) in row" :key="index">
                            <div class="col dp-months" :class="{current:month.current}" @click="setMonth(month.jsMonth)" >
                                {{ month.name.substr(0,3) }}
                            </div>
                        </template>
                    </div>
                </div>
                </transition>
                <transition name="trans-dp-picker-zoom" class="position-absolute mw-picker" id="dateRangePickerBody">
                    <div v-if="currentView=='years'">
                        <div class="row row-years" v-for="(row,i) in displayed_year_rows" :key="i" :class="{'mt-5':i==0}">
                            <template v-for="(y,index) in row" :key="index">
                                <div class="col dp-years" :class="{current:y.current}" @click="setYear(y.year)" >
                                    {{y.year}}
                                </div>
                            </template>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
    <div class="hint" v-if="hint"> {{ hint }} </div>
</template>

<script>
    import {ref, nextTick, watch, computed, onUpdated} from 'vue';
    import {useStore} from 'vuex';
    import {GET_CURRENT_SELECT, SELECT_MODULE, SET_CURRENT_SELECT} from "../../store/types/types";

    export default {
        name: "DateRangePicker",
        props:{
            modelValue: Object,
            droppos: Object,
            label:String,
            placeholder:{
                type: String,
                default: 'Day'
            },
            disabled:Boolean,
            hint:String,
            availableDates:Array,
            disabledToDate:String,
            disabledFromDate:String,
            color: {
                type: String,
                default: '#47454B'
            },
            font: {
                type: String,
                default: '0.875rem'
            },
            name:{
                type: String,
                required: true
            },
        },
        emits: ['update:modelValue'],
        setup(props,context){

            const store=useStore();
            const selectedCount = ref(0);
            const start_date=ref([]);
            const end_date=ref([]);
            const displayed_months_rows = ref({});
            const displayed_year_rows = ref({});
            let sel = computed(()=>store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]);
            const days=[
                {
                    dayName:'Mo',
                    jsDay:1
                },
                {
                    dayName:'Tu',
                    jsDay:2
                },
                {
                    dayName:'We',
                    jsDay:3
                },
                {
                    dayName:'Th',
                    jsDay:4
                },
                {
                    dayName:'Fr',
                    jsDay:5
                },
                {
                    dayName:'Sa',
                    jsDay:6
                },
                {
                    dayName:'Su',
                    jsDay:1
                },
            ];

            const monthsName= {
                    0:{
                        name:"January"
                    },
                    1:{
                        name:"Febuary"
                    },
                    2:{
                        name:"March"
                    },
                    3:{
                        name:"April"
                    },
                    4:{
                        name:"May"
                    },
                    5:{
                        name:"June"
                    },
                    6:{
                        name:"July"
                    },
                    7:{
                        name:"August"
                    },
                    8:{
                        name:"September"
                    },
                    9:{
                        name:"October"
                    },
                    10:{
                        name:"November"
                    },
                    11:{
                        name:"December"
                    }
                };
            const currentView = ref('dates');

            const MonthYear = ref({});
            const formated_date = ref('');

            if(props.modelValue.start !=="" ){
                start_date.value = props.modelValue.start.split('-');
                end_date.value = props.modelValue.end.split('-');
                MonthYear.value.month=(start_date.value[1]-1);
                MonthYear.value.year=parseInt(start_date.value[0]);
            }else{
                const d = new Date();
                MonthYear.value.month= d.getMonth();
                MonthYear.value.year = d.getFullYear();
            }
            function minusMonth() {

                if(MonthYear.value.month==0){
                    MonthYear.value.month=11;
                    MonthYear.value.year--;
                }else{
                    MonthYear.value.month--;
                }
                adjustPickerHeight();
            }
            function plusMonth() {

                if(MonthYear.value.month==11){
                    MonthYear.value.month=0;
                    MonthYear.value.year++;
                }else{
                    MonthYear.value.month++;
                }
                adjustPickerHeight();
            }
            const displayed_dates = ref([]);
            const displayed_dates_rows = ref({});

            function renderPicker() {
                if(
                    typeof start_date.value[0]!="undefined" &&
                    typeof start_date.value[1]!="undefined" &&
                    typeof start_date.value[2]!="undefined" &&
                    typeof end_date.value[0]!="undefined" &&
                    typeof end_date.value[1]!="undefined" &&
                    typeof end_date.value[2]!="undefined" ){
                    formated_date.value = `${start_date.value[2].toString().padStart(2, "0")}/${start_date.value[1].toString().padStart(2, "0")}/${start_date.value[0]} ~ ${end_date.value[2].toString().padStart(2, "0")}/${end_date.value[1].toString().padStart(2, "0")}/${end_date.value[0]}`;
                }
                displayed_dates_rows.value = {0: [], 1: [], 2: [], 3: [], 4: [], 5: []};
                displayed_dates.value = [];

                let firstDayofMonth = new Date(MonthYear.value.year, MonthYear.value.month, 1).getDay();
                let lastDateofMonth = new Date(MonthYear.value.year, MonthYear.value.month + 1, 0).getDate();
                // console.log('Current=>Month-Year=>'+MonthYear.value.month+"-"+MonthYear.value.year);
                // console.log('firstDayofMonth=>'+firstDayofMonth);
                // console.log('lastDateofMonth=>'+lastDateofMonth);
                let lastMonth = (MonthYear.value.month == 0 ? 11 : MonthYear.value.month - 1);
                let lastMonthYear = (MonthYear.value.month == 0 ? MonthYear.value.year - 1 : MonthYear.value.year);
                // console.log('lastMonth=>'+lastMonth);
                // console.log('lastMonthYear=>'+lastMonthYear);
                let lastMonthEnd = new Date(lastMonthYear, lastMonth + 1, 0).getDate();
                // console.log('lastMonthEnd=>'+lastMonthEnd);
                let calendarStarts = (lastMonthEnd - firstDayofMonth + 2);
                let notavailable = typeof props.availableDates !="undefined" && props.availableDates.length > 0;
                if(calendarStarts <= lastMonthEnd){
                    while (calendarStarts <= lastMonthEnd) {
                        displayed_dates.value.push({
                            date: calendarStarts,
                            month: lastMonth,
                            year: lastMonthYear,
                            current_month: false,
                            selected: false,
                            start: false,
                            end: false,
                            period: false,
                            notavailable: notavailable,
                        })
                        calendarStarts++;
                    }
                }
                if(calendarStarts > lastMonthEnd &&  firstDayofMonth == 0){
                    calendarStarts = lastMonthEnd - 5;
                    while (calendarStarts <= lastMonthEnd) {
                        displayed_dates.value.push({
                            date: calendarStarts,
                            month: lastMonth,
                            year: lastMonthYear,
                            current_month: false,
                            selected: false,
                            start: false,
                            end: false,
                            period: false,
                            notavailable: notavailable,
                        })
                        calendarStarts++;
                    }
                }
                let date = 1;
                while (date <= lastDateofMonth) {
                    displayed_dates.value.push({
                        date: date,
                        month: MonthYear.value.month,
                        year: MonthYear.value.year,
                        current_month: true,
                        selected: false,
                        start: false,
                        end: false,
                        period: false,
                        notavailable:notavailable,
                    });
                    date++;
                }

                date = 1;
                while (displayed_dates.value.length < 35) {
                    displayed_dates.value.push({
                        date: date,
                        month: (MonthYear.value.month == 11 ? 0 : MonthYear.value.month + 1),
                        year: (MonthYear.value.month == 11 ? MonthYear.value.year + 1 : MonthYear.value.year),
                        current_month: false,
                        selected: false,
                        start: false,
                        end: false,
                        period: false,
                        notavailable:notavailable,
                    });
                    date++;
                }
                date = 1;
                if (displayed_dates.value.length > 35) {
                    while (displayed_dates.value.length < 42) {
                        displayed_dates.value.push({
                            date: date,
                            month: (MonthYear.value.month == 11 ? 0 : MonthYear.value.month + 1),
                            year: (MonthYear.value.month == 11 ? MonthYear.value.year + 1 : MonthYear.value.year),
                            current_month: false,
                            selected: false,
                            start: false,
                            end: false,
                            period: false,
                            notavailable:notavailable,
                        });
                        date++;
                    }
                }


                let index = 0;
                let count = 0
                let datestr='';

                for (let i in displayed_dates.value) {
                    datestr = `${displayed_dates.value[i].year}-${(parseInt(displayed_dates.value[i].month)+1).toString().padStart(2,"0")}-${displayed_dates.value[i].date.toString().padStart(2,"0")}`
                    if(typeof props.availableDates!="undefined"&& props.availableDates.includes(datestr))
                        displayed_dates.value[i].notavailable=false;
                    //disabledToDate
                    // if(typeof props.disabledToDate!="undefined"&&props.disabledToDate!=""){
                        // let disabledto=new Date(props.disabledToDate);
                        // let curdate=new Date(datestr);
                        // if(curdate<=disabledto){
                        //     displayed_dates.value[i].notavailable=true;
                        // }
                    // }
                     //disabledFromDate
                    // if(typeof props.disabledFromDate!="undefined"&&props.disabledFromDate!=""){
                        // let disabledFrom=new Date(props.disabledFromDate);
                        // let curdate=new Date(datestr);
                        // if(curdate>disabledFrom){
                        //     displayed_dates.value[i].notavailable=true;
                        // }
                    // }

                    displayed_dates.value[i].selected = false;
                    displayed_dates.value[i].start = false;
                    displayed_dates.value[i].end = false;
                    displayed_dates.value[i].period = false;

                    if (count == 7) {
                        index++;
                        count = 0;
                    }
                    if (start_date.value.length > 0 && selectedCount.value == 1){
                        let curdate=new Date( displayed_dates.value[i].year, parseInt(displayed_dates.value[i].month) +1,  displayed_dates.value[i].date);
                        let start = new Date( start_date.value[0], start_date.value[1], start_date.value[2]);
                        if (curdate.getTime() == start.getTime()) {
                            displayed_dates.value[i].start = true;
                            displayed_dates.value[i].selected = true;
                            displayed_dates.value[i].period = false;
                        }
                    }
                    if (start_date.value.length > 0 && end_date.value.length > 0){
                        let curdate=new Date( displayed_dates.value[i].year, parseInt(displayed_dates.value[i].month) +1,  displayed_dates.value[i].date);
                        let start = new Date( parseInt(start_date.value[0]), parseInt(start_date.value[1]), parseInt(start_date.value[2]));
                        let end = new Date( parseInt(end_date.value[0]), parseInt(end_date.value[1]), parseInt(end_date.value[2]) );
                        if (curdate.getTime() == end.getTime()) {
                            displayed_dates.value[i].end = true;
                            displayed_dates.value[i].selected = true;
                        }
                        if (curdate.getTime() == start.getTime()) {
                            displayed_dates.value[i].start = false;
                            displayed_dates.value[i].selected = false;
                            displayed_dates.value[i].period = true;
                        }
                        if( curdate.getTime() > start.getTime() && curdate.getTime() < end.getTime()){
                            displayed_dates.value[i].period = true;
                        }
                    }
                    displayed_dates_rows.value[index].push(displayed_dates.value[i]);
                    count++;
                }
            }
            function adjustPickerHeight(){
                nextTick(()=>{
                    let paddingHeight = 50;
                    let datePickerHeader = document.querySelector('#dateRangePickerHeader');
                    let datePickerBody = document.querySelector('#dateRangePickerBody');
                    let datePicker = document.querySelector('#dateRangePicker');
                    datePicker.style.minHeight =  paddingHeight + datePickerHeader.clientHeight + datePickerBody.clientHeight + 'px';
                })
            }
            renderPicker();

            watch(() => _.cloneDeep(MonthYear), (current_val, previous_val) => {
                renderPicker();
                renderMonthsView();
                renderYearsView();
            });
            watch(() => _.cloneDeep(start_date), (current_val, previous_val) => {
                renderPicker();
            });
            function resetFilter(){
                start_date.value = [];
                end_date.value = [];
                formated_date.value = '';
                selectedCount.value = 0;
                context.emit(
                    "update:modelValue", {
                        start: '',
                        end: ''
                    }
                );
                renderPicker();
            }
            function setDate(y,m,d) {
                if( selectedCount.value == 0 ){
                    start_date.value[0]= parseInt(y);
                    start_date.value[1]= parseInt(m)+1;
                    start_date.value[2]= parseInt(d);
                    end_date.value = [];
                    selectedCount.value = 1;
                    renderPicker();
                }else{
                    let start = new Date(start_date.value[0], start_date.value[1], start_date.value[2]);
                    let end = new Date( parseInt(y), parseInt(m)+1, parseInt(d));
                    if(start.getTime() > end.getTime()){
                        start_date.value[0] = parseInt(y);
                        start_date.value[1] = parseInt(m)+1;
                        start_date.value[2] = parseInt(d);
                        end_date.value = [];
                        renderPicker();
                        return false;
                    }else{
                        end_date.value[0]= parseInt(y);
                        end_date.value[1]= parseInt(m)+1;
                        end_date.value[2]= parseInt(d);
                        renderPicker();
                        selectedCount.value = 0;
                        context.emit(
                            "update:modelValue", {
                                start: `${start_date.value[0]}-${start_date.value[1].toString().padStart(2, "0")}-${start_date.value[2].toString().padStart(2, "0")}`,
                                end: `${end_date.value[0]}-${end_date.value[1].toString().padStart(2, "0")}-${end_date.value[2].toString().padStart(2, "0")}`
                            }
                        );
                    }
                    toggleshowDp();
                }
            }
            function setMonth(m){
               // start_date.value[1]=parseInt(m)+1;
                MonthYear.value.month=parseInt(m);
                currentView.value='dates';

            }
            function setYear(y){
                MonthYear.value.year=parseInt(y);
                currentView.value='dates';
            }
            function showYears() {
                currentView.value='years';
                renderYearsView();
                adjustPickerHeight();
            }
            function showMonths() {
                currentView.value='months'
                renderMonthsView();
                adjustPickerHeight();
            }

            function renderMonthsView() {

                displayed_months_rows.value= {0: [], 1: [], 2: [], 3: []};

                let index = 0;
                let count = 0
              for (const jsMonth in   monthsName) {
                  monthsName[jsMonth].current=false;
                  monthsName[jsMonth].jsMonth=jsMonth;
                  if(MonthYear.value.month ==jsMonth)
                      monthsName[jsMonth].current=true;


                  if (count == 3) {
                      index++;
                      count = 0;
                  }
                  displayed_months_rows.value[index].push(monthsName[jsMonth]);
                  count++;
                }

            }

            function renderYearsView() {

                displayed_year_rows.value= {0: [], 1: [], 2: []};

                let index = 0;
                let count = 0
                let startYr=MonthYear.value.year-4;
                let endYr=MonthYear.value.year+4;
                while (startYr<=endYr) {
                    if (count == 3) {
                        index++;
                        count = 0;
                    }
                    displayed_year_rows.value[index].push({year:startYr,current:MonthYear.value.year==startYr});
                    count++;
                    startYr++;
                }

            }
            function toggleshowDp() {
                nextTick(()=>{
                    store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`, sel.value === props.name ? '' : props.name);
                }).then(()=>{
                    sel = computed(()=>store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]);
                });
            }
            // document.addEventListener('click', (event)=>{
            //     if(!event.target.matches(".date-picker") && !event.target.closest(".dp-picker")){
            //         nextTick(()=>{
            //             store.commit(`${SELECT_MODULE}${SET_CURRENT_SELECT}`, '');
            //         }).then(()=>{
            //             sel = computed(()=>store.getters[`${SELECT_MODULE}${GET_CURRENT_SELECT}`]);
            //         });
            //     }
            // });
            return {
                sel,
                minusMonth,
                plusMonth,
                MonthYear,
                days,
                setDate,
                formated_date,
                displayed_dates,
                monthsName,
                displayed_year_rows,
                displayed_months_rows,
                displayed_dates_rows,
                currentView,
                showMonths,
                showYears,
                setMonth,
                setYear,
                toggleshowDp,
                resetFilter
            }

        },
        methods:{

        }
    }
</script>

<style scoped>
    .mw-picker{
        width: calc(100% - 48px);
    }
    input{
        border: 0.5px solid #E0E0E0;
        box-sizing: border-box;
        border-radius: 4px;
        background: transparent url('../../../img/calendar.svg') no-repeat center right 10px;
        background-size: 12px;
        height: 40px;
        line-height: 40px;
        padding-left: 15px;
        color: #47454B;
        vertical-align: middle;
        font-size: 0.875rem;
        padding-right: 30px;
    }
    input::placeholder{
        color: #000000;
    }
    .btn-dp{
        width: 44px;
        height: 44px;
        position: relative;
        background: #FFFFFF url('../../../img/chevron.svg') no-repeat center center;
        background-size: 8px;
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.2);
        border-radius: 80px;
        transition: all 0.2s ease-in-out;
    }

    .btn-dp.minus{
        transform: rotate(180deg);
        box-shadow: 0px -1px 1px rgba(0, 14, 51, 0.2);
    }
    .btn-dp:hover{

        color: #fff;
        background:#47454B url('../../../img/chevron_w.svg') no-repeat center center;
        background-size: 8px;
    }
    .dp{
        position: relative;
    }
    .dp-picker{
        position: absolute;
        width: 426px;
        min-height: 426px;
        background: #F5F5F5;
        box-shadow: 0px 1px 2px rgba(0, 14, 51, 0.25);
        border-radius: 16px;
        z-index: 100;
        padding: 24px;
        transform-origin: top left;
        transition: min-height 0.3s ease-in-out;
    }
    .dp-picker.row6{
        min-height: 426px;
    }
    .dp-dayname{
        font-weight: 500;
        text-align: center;
        max-width: 50px;
        height: 44px;
        line-height: 44px;
    }
    .dp-dates{
        text-align: center;
        padding: 0;
        max-width: 50px;
        height: 44px;
        background: #FFFFFF;
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        border-radius: 6px;
        line-height: 44px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .dp-dates.disabled{
        pointer-events: none;
        background: transparent;
        color:rgba(0, 23, 84, 0.15);
        box-shadow: none;
        cursor: default;
    }
    .dp-dates:not(.disabled):not(.current):hover{
        background-color: #47454B;
        color: #fff;
    }
    .dp-dates.current{
        background-color: #0047FF;
        color: #FFF;

    }
    .dp-dates.disabled.current{
        opacity: 0.5;
    }

    .dp-months{
        text-align: center;
        padding: 0;
        max-width: 50px;
        height: 44px;
        background: #FFFFFF;
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        border-radius: 6px;
        line-height: 44px;
        font-weight: 800;
        font-family: Gilroy;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .dp-months.disabled{
        pointer-events: none;
        background: transparent;
        color:rgba(0, 23, 84, 0.15);
        box-shadow: none;
        cursor: default;
    }
    .dp-months:not(.disabled):not(.current):hover{
        background-color: #47454B;
        color: #fff;
    }
    .dp-months.current{
        background-color: #0047FF;
        color: #FFF;

    }
    .dp-months.disabled.current{
        opacity: 0.5;
    }
    .dp-years{
        text-align: center;
        padding: 0;
        max-width: 50px;
        height: 44px;
        background: #FFFFFF;
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        border-radius: 6px;
        line-height: 44px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .dp-years.disabled{
        pointer-events: none;
        background: transparent;
        color:rgba(0, 23, 84, 0.15);
        box-shadow: none;
        cursor: default;
    }
    .dp-years:not(.disabled):not(.current):hover{
        background-color: #47454B;
        color: #fff;
    }
    .dp-years.current{
        background-color: #0047FF;
        color: #FFF;

    }
    .dp-years.disabled.current{
        opacity: 0.5;
    }
    .dp .row{
        justify-content: space-evenly;
        margin-bottom: 4px;
        font-size: 16px;
        align-items: center;
    }
    .dp .row-months{
        margin-bottom: 20px;
    }
    .dp .row-years{
        margin-bottom: 40px;
    }
    .btn-month,.btn-year{
        background: #FFFFFF;
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        border-radius: 6px;
        height: 44px;
        font-weight: 800;
        font-family: Gilroy;
        vertical-align: middle;
        font-size: 22px;
        position: relative;
        transition: all 0.2s ease-in-out;
        padding: 7px 22px 7px 14px;
    }
    .btn-month{
        margin-right: 2px;
    }
    .btn-month:hover,.btn-year:hover{
        background-color: #47454B;
        color: #fff;
    }
    .btn-month:after,.btn-year:after{
        content:"";
        width:8px;
        height: 8px;
        border: 4px solid transparent;
        border-right-color: #0047FF;
        border-bottom-color: #0047FF;
        border-radius: 3px;
        top:24px;
        right: 11px;
        position: absolute;
        transition: all 0.2s ease-in-out;
    }
    .btn-month:hover:after,.btn-year:hover:after{
        border-right-color: #FFF;
        border-bottom-color: #FFF;
    }

    .trans-dp-picker-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-dp-picker-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-dp-picker-enter-active{
        transition: all ease 0.2s;
    }
    .trans-dp-picker-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-dp-picker-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-dp-picker-leave-active{
        transition: all ease 0.2s;
    }

    .trans-dp-picker-zoom-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-dp-picker-zoom-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .trans-dp-picker-zoom-enter-active{
        transition: all ease 0.2s;
    }
    .trans-dp-picker-zoom-leave-from{
        opacity: 1;
        transform: scale(1);
    }
    .trans-dp-picker-zoom-leave-to{
        opacity: 0;
        transform: scale(0.6);
    }
    .trans-dp-picker-zoom-leave-active{
        transition: all ease 0.2s;
    }
    .hint{
        margin-bottom: 20px;
        font-size: 16px;
        color: #757575;;
        font-weight: 300;
    }
    .notavailable{
        color: #EB5757;
        background: rgba(245, 171, 171, 0.7);
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        pointer-events: none;
    }
    .period{
        color: #EB5757;
        background: rgba(245, 171, 171, 0.7);
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        opacity: .8;
    }
    .start{
        color: greenyellow;
        background: rgba(245, 171, 171, 0.7);
        box-shadow: 0px 1px 1px rgba(0, 14, 51, 0.05);
        opacity: .8;
    }
</style>
