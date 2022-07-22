<template>
    <div class="w-100 position-relative bg-black">
        <div class="d-flex flex-wrap p-2">
            <div class="col-sm-6 col-md-4">
                <div class="each-home-stats">
                    <div class="bg-white p-2 m-2 rounded-3">
                        <div class="d-flex mb-3">
                            <div class="col-2 as-stats-label fw-bold font-20 pl-4">Due<br/>Today</div>
                            <div class="col-2 px-2 text-end total_stats_num">
                                {{ mainStats.total_due_today ? mainStats.total_due_today : 0}}
                            </div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center rounded-pill">
                                    <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_today_deliveries<15?
                                    15:(mainStats.percent_today_deliveries>85?85:mainStats.percent_today_deliveries))+'%'}">
                                    {{ mainStats.due_today_deliveries ? parseInt(mainStats.due_today_deliveries) : 0}}</div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_today<15?
                                    15:(mainStats.percent_today_stores>85?85:mainStats.percent_today_stores))+'%'}">
                                    {{mainStats.due_today_stores ? parseInt(mainStats.due_today_stores) : 0}}</div>
                                </div>
                                <div class="d-flex count-under-label">
                                    <div class="col-9">Deliveries</div>
                                    <div class="col-3 text-end">Stores</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="col-2 as-stats-label pl-4">Deliveries</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_today_deliveries ? mainStats.due_today_deliveries : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                    <div class="standard-bar py-1 px-2"
                                    :style="{'width':+(mainStats.percent_today_inprocess_deliveries<15?
                                    15:(mainStats.percent_today_inprocess_deliveries>85?
                                    85:mainStats.percent_today_inprocess_deliveries))+'%'}"
                                    :class="{'col-6':mainStats.due_today_deliveries==0,'px-0':mainStats.due_today_deliveries==0}">
                                    {{ mainStats.percent_today_inprocess_deliveries ? parseInt(mainStats.percent_today_inprocess_deliveries) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_today_done_deliveries<15?
                                    15:(mainStats.percent_today_done_deliveries>85?
                                    85:mainStats.percent_today_done_deliveries))+'%'}"
                                    :class="{'col-6':mainStats.due_today_deliveries==0,'px-0':mainStats.due_today_deliveries==0}">
                                    {{mainStats.percent_today_done_deliveries ? parseInt(mainStats.percent_today_done_deliveries) : 0}}%
                                    </div>
                                </div>
                                <div class="d-flex count-under-label">
                                    <div class="col-9">In process</div>
                                    <div class="col-3 text-end">Done</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="col-2 as-stats-label font-20 pl-4">Stores</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_today_stores ? mainStats.due_today_stores : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                    <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_today_inprocess_stores<15?
                                    15:(mainStats.percent_today_inprocess_stores>85?
                                    85:mainStats.percent_today_inprocess_stores))+'%'}"
                                    :class="{'col-6':mainStats.due_today_stores==0,'px-0':mainStats.due_today_stores==0}">
                                    {{ mainStats.percent_today_inprocess_stores? parseInt(mainStats.percent_today_inprocess_stores) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_today_done_stores<15?
                                    15:(mainStats.percent_today_done_stores>85?
                                    85:mainStats.percent_today_done_stores))+'%'}"
                                    :class="{'col-6':mainStats.due_today_stores==0,'px-0':mainStats.due_today_stores==0}">
                                    {{ mainStats.percent_today_done_stores? parseInt(mainStats.percent_today_done_stores) : 0 }}%
                                    </div>
                                </div>
                                <div class="d-flex count-under-label">
                                    <div class="col-9">In process</div>
                                    <div class="col-3 text-end">Done</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="each-home-stats">
                    <div class="bg-white p-2 m-2 rounded-3">
                        <div class="d-flex mb-3">
                            <div class="col-2 as-stats-label fw-bold font-20 pl-4">Due<br/>Tomorrow</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.total_due_tomorrow ? mainStats.total_due_tomorrow : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center">
                                    <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_deliveries<15?
                                    15:(mainStats.percent_tomorrow_deliveries>85?85:mainStats.percent_tomorrow_deliveries))+'%'}">
                                    {{mainStats.due_tomorrow_deliveries ? mainStats.due_tomorrow_deliveries : 0 }}</div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_tomorrow<10?
                                    10:(mainStats.percent_tomorrow_stores>85?85:mainStats.percent_tomorrow_stores))+'%'}">
                                    {{mainStats.due_tomorrow_stores ? mainStats.due_tomorrow_stores : 0}}</div>
                                </div>
                                <div class="d-flex count-under-label">
                                    <div class="col-9">Deliveries</div>
                                    <div class="col-3 text-end">Stores</div>
                                </div>
                            </div>
                        </div>
                        <!--Percent-->
                        <div class="d-flex mb-3">
                            <div class="col-2 as-stats-label pl-4">Deliveries</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_tomorrow_deliveries ? mainStats.due_tomorrow_deliveries : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                    <div class="standard-bar py-1 px-2"
                                    :style="{'width':+(mainStats.percent_tomorrow_inprocess_deliveries<15?
                                    15:(mainStats.percent_tomorrow_inprocess_deliveries>85?
                                    85:mainStats.percent_tomorrow_inprocess_deliveries))+'%'}"
                                    :class="{'col-6':mainStats.due_tomorrow_deliveries==0,'px-0':mainStats.due_tomorrow_deliveries==0}">
                                    {{mainStats.percent_tomorrow_inprocess_deliveries ? parseInt(mainStats.percent_tomorrow_inprocess_deliveries) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_done_deliveries<15?
                                    15:(mainStats.percent_tomorrow_done_deliveries>85?
                                    85:mainStats.percent_tomorrow_done_deliveries))+'%'}"
                                    :class="{'col-6':mainStats.due_tomorrow_deliveries==0,'px-0':mainStats.due_tomorrow_deliveries==0}">
                                    {{ mainStats.percent_tomorrow_done_deliveries ? parseInt(mainStats.percent_tomorrow_done_deliveries) : 0}}%
                                    </div>
                                </div>
                                <div class="row count-under-label">
                                    <div class="col-9">In process</div>
                                    <div class="col-3 text-end">Done</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="col-2 as-stats-label pl-4">Stores</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_tomorrow_stores ? mainStats.due_tomorrow_stores : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                    <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_inprocess_stores<15?
                                    15:(mainStats.percent_tomorrow_inprocess_stores>85?
                                    85:mainStats.percent_tomorrow_inprocess_stores))+'%'}"
                                    :class="{'col-6':mainStats.due_tomorrow_stores==0,'px-0':mainStats.due_tomorrow_stores==0}">
                                    {{ mainStats.percent_tomorrow_inprocess_stores ? parseInt(mainStats.percent_tomorrow_inprocess_stores) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_done_stores<15?
                                    15:(mainStats.percent_tomorrow_done_stores>85?
                                    85:mainStats.percent_tomorrow_done_stores))+'%'}"
                                    :class="{'col-6':mainStats.due_tomorrow_stores==0,'px-0':mainStats.due_tomorrow_stores==0}">
                                    {{mainStats.percent_tomorrow_done_stores ? parseInt(mainStats.percent_tomorrow_done_stores) : 0}}%
                                    </div>
                                </div>
                                <div class="row count-under-label">
                                    <div class="col-9">In process</div>
                                    <div class="col-3 text-end">Done</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="each-home-stats">
                    <div class="bg-white p-2 m-2 rounded-3">
                    <div class="d-flex mb-3">
                        <div class="col-2 as-stats-label fw-bold font-20 pl-4">Due<br/> <span class="text-nowrap fw-bold as-stats-label">Day after</span></div>
                        <div class="col-2 px-2 text-end total_stats_num">{{mainStats.total_due_later ? mainStats.total_due_later : 0}}</div>
                        <div class="col-8 pr-5">
                            <div class="red-stats text-white d-flex justify-content-between text-center">
                                <div class="standard-bar py-1 px-2"
                                    :style="{'width':+ ( mainStats.percent_later_deliveries < 15 ? 15: ( mainStats.percent_later_deliveries>85?85:mainStats.percent_later_deliveries))+'%'}">
                                {{mainStats.due_later_deliveries ? mainStats.due_later_deliveries : 0 }}</div>
                                <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_later<15?
                                15:(mainStats.percent_later_stores>85?85:mainStats.percent_later_stores))+'%'}">
                                {{mainStats.due_later_stores ? mainStats.due_later_stores : 0}}</div>
                            </div>
                            <div class="d-flex count-under-label">
                                <div class="col-9">Deliveries</div>
                                <div class="col-3 text-end">Stores</div>
                            </div>
                        </div>
                    </div>
                    <!--Percent-->
                    <div class="d-flex mb-3">
                        <div class="col-2 as-stats-label pl-4">Deliveries</div>
                        <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_later_deliveries ? mainStats.due_later_deliveries : 0}}</div>
                        <div class="col-8 pr-5">
                            <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                <div class="standard-bar py-1 px-2"
                                :style="{'width':+(mainStats.percent_later_inprocess_deliveries<15?
                                15:(mainStats.percent_later_inprocess_deliveries>85?
                                85:mainStats.percent_later_inprocess_deliveries))+'%'}"
                                :class="{'col-6':mainStats.due_later_deliveries==0,'px-0':mainStats.due_later_deliveries==0}">
                                {{mainStats.percent_later_inprocess_deliveries ? parseInt(mainStats.percent_later_inprocess_deliveries) : 0}}%
                                </div>
                                <div class="py-1 px-2" :style="{'width':+(mainStats.percent_later_done_deliveries<15?
                                15:(mainStats.percent_later_done_deliveries>85?
                                85:mainStats.percent_later_done_deliveries))+'%'}"
                                :class="{'col-6':mainStats.due_later_deliveries==0,'px-0':mainStats.due_later_deliveries==0}">
                                {{ mainStats.percent_later_done_deliveries ? parseInt(mainStats.percent_later_done_deliveries) : 0}}%
                                </div>
                            </div>
                            <div class="row count-under-label">
                                <div class="col-9">In process</div>
                                <div class="col-3 text-end">Done</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="col-2 as-stats-label pl-4">Stores</div>
                        <div class="col-2 px-2 text-end total_stats_num">{{mainStats.due_later_stores ? mainStats.due_later_stores : 0}}</div>
                        <div class="col-8 pr-5">
                            <div class="red-stats text-white d-flex justify-content-between text-center is_percent">
                                <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_later_inprocess_stores<15?
                                15:(mainStats.percent_later_inprocess_stores>85?
                                85:mainStats.percent_later_inprocess_stores))+'%'}"
                                :class="{'col-6':mainStats.due_later_stores==0,'px-0':mainStats.due_later_stores==0}">
                                {{ mainStats.percent_later_inprocess_stores ? parseInt(mainStats.percent_later_inprocess_stores) : 0}}%
                                </div>
                                <div class="py-1 px-2" :style="{'width':+(mainStats.percent_later_done_stores<15?
                                15:(mainStats.percent_later_done_stores>85?
                                85:mainStats.percent_later_done_stores))+'%'}"
                                :class="{'col-6':mainStats.due_later_stores==0,'px-0':mainStats.due_later_stores==0}">
                                {{mainStats.percent_later_done_stores ? parseInt(mainStats.percent_later_done_stores) : 0}}%
                                </div>
                            </div>
                            <div class="row count-under-label">
                                <div class="col-9">In process</div>
                                <div class="col-3 text-end">Done</div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 d-flex bg-black" id="assembly-home-stats">
        <div class="col-12 p-0">
            <div class="col-12 p-0">
                <div v-if="groupedPostes">
                    <div class="bg-white mb-3 p-3">
                        <div class="d-flex mb-3">
                            <div class="w-13"><h2 id="order_status_heading" class="mt-1 mb-0 mx-0">Items in <br>Production</h2></div>
                            <div class="w-87 mt-auto">
                                <div class="d-flex justify-content-between">
                                    <div v-for="(a, index) in groupedPostes" :style="{width: parseInt(100/groupedPostes.length)+'%'}" class="text-center each-poste-label" :key="index">
                                        <!-- <span class="text-capitalize" v-if="a.group_name=='Partner'">With<br/></span>
                                        <span class="text-capitalize" v-else-if="a.group_name!='Storage'&&a.group_name!='Conveyor'">In<br/></span>
                                        <span class="text-capitalize" v-if="a.group_name!='QC 1' && a.group_name!='QC 2'"></span>
                                        <span class="text-capitalize" v-else>&nbsp;</span>
                                        <span class="text-capitalize" v-if="a.group_name == 'Conveyor'">On <br> <span class="text-nowrap">Assembly Conveyor</span></span>
                                        <span class="text-capitalize" v-else-if="a.group_name=='Storage'">On<br> <span class="text-nowrap">Storage Conveyor</span></span>
                                        <span class="text-capitalize text-nowrap" v-else>{{ a.formatted_name }}</span> -->
                                        <span class="text-capitalize" v-html="a.formatted_name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Row today-->
                        <div class="d-flex align-items-center">
                            <div class="w-13"></div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between total_row">
                                    <div v-for="(a, index) in groupedPostes" :style="{width:groupedPosteWidth+'%'}" class="text-center" :key="index">
                                        <span class="w-100 each-poste-stats ps-xl-2 pe-xl-2 fw-bold">{{assemblyStatsTotal[a.group_name]}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center d-none">
                            <div class="w-13"><span class="fst-italic">Avg time</span></div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between total_row">
                                    <div v-for="(a, index) in groupedPostes" :style="{width:groupedPosteWidth+'%'}" class="text-center" :key="index">
                                        <span class=""> 4 hr </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="w-13">
                                <div class="w-100 standard-label fw-bold mb-2">Due<br/>Today</div>
                                <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                            </div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between">
                                    <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsToday[a.group_name].std === 0 && assemblyStatsToday[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'today','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                        <div class="w-100 text-center each-poste-stats poste-stats-top pt-3 pb-2 grey-bg">
                                            <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','all')">{{assemblyStatsToday[a.group_name].all}}</a>
                                        </div>
                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg pb-2 delivery_store" >
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','delivery')">{{assemblyStatsToday[a.group_name].delivery}}</a></p>
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','store')">{{assemblyStatsToday[a.group_name].store}}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Spacer-->
                        <div class="d-flex py-2"></div>
                        <!--Row tommorow-->
                        <div class="d-flex align-items-center">
                            <div class="w-13">
                                <div class="w-100 standard-label fw-bold mb-2">Due<br/>Tomorrow</div>
                                <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                            </div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between">
                                    <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsTomorrow[a.group_name].std === 0 && assemblyStatsTomorrow[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'tomorrow','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                        <div class="w-100 text-center each-poste-stats poste-stats-top pt-3 pb-2 grey-bg">
                                            <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','all')">{{assemblyStatsTomorrow[a.group_name].all}}</a>
                                        </div>
                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg pb-2 delivery_store" >
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','delivery')">{{assemblyStatsTomorrow[a.group_name].delivery}}</a></p>
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','store')">{{assemblyStatsTomorrow[a.group_name].store}}</a></p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Spacer-->
                        <div class="d-flex py-2"></div>

                        <!--Row Overdue-->
                        <div class="d-flex align-items-center">
                            <div class="w-13">
                                <div class="w-100 standard-label mb-2 fw-bold">All<br/>Overdues</div>
                                <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                            </div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between">
                                    <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsOverdue[a.group_name].std === 0 && assemblyStatsOverdue[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'overdue','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                        <div class="w-100 text-center each-poste-stats poste-stats-top pt-3 pb-2 grey-bg">
                                            <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','all')">{{assemblyStatsOverdue[a.group_name].all}}</a>
                                        </div>
                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg pb-2 delivery_store" >
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','delivery')">{{assemblyStatsOverdue[a.group_name].delivery}}</a></p>
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','store')">{{assemblyStatsOverdue[a.group_name].store}}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Spacer-->
                        <div class="d-flex py-2"></div>

                        <!--Row Later-->
                        <div class="d-flex align-items-center">
                            <div class="w-13">
                                <div class="w-100 standard-label mb-2 fw-bold">Due<br/>Later</div>
                                <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                            </div>
                            <div class="w-87">
                                <div class="d-flex justify-content-between">
                                    <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc rounded-2" :class="{'is_bloc_disabled':assemblyStatsLater[a.group_name].std === 0 && assemblyStatsLater[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'later','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                        <div class="w-100 text-center each-poste-stats poste-stats-top pt-3 pb-2 grey-bg">
                                            <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','all')">{{assemblyStatsLater[a.group_name].all}}</a>
                                        </div>
                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom pb-2 grey-bg delivery_store" >
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','delivery')">{{assemblyStatsLater[a.group_name].delivery}}</a></p>
                                            <p class="mb-0"><a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','store')">{{assemblyStatsLater[a.group_name].store}}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex bg-white mb-3 d-none" id="stats_table">
                        <div class="text-white col-12 dttable" id="invoice-list-main">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th v-for="(item, index) in columns" :class="item.thClass" :key="index" style="border-bottom: 2px solid #dee2e6 !important">
                                            <check-box v-if="item.key == 'selected' && invoiceList.length" :checked_checkbox="(invoiceList.length==MULTI_SELECTED.length)" @checkbox-clicked="checkboxallclicked"></check-box>
                                            <span v-else v-html="item.label"></span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <transition-group name="list" appear>
                                        <tr v-for="(invoiceRow, index) in invoiceList" :key="index" class="trow" :class="{current_sel:invoiceRow.item_id == CURRENT_SELECTED}"
                                            @click="selectrow(invoiceRow.item_id)"
                                        >
                                            <td>
                                                <check-box :checked_checkbox="(invoiceRow.item_id == CURRENT_SELECTED) || MULTI_SELECTED.includes(invoiceRow.item_id)" :id="invoiceRow.item_id" @checkbox-clicked="checkboxclicked"></check-box>
                                            </td>

                                            <td class="text-capitalize fw-16"><span>{{ invoiceRow.item_id }}</span></td>

                                            <!-- Customer Name -->
                                            <td class="text-capitalize fw-16">{{ invoiceRow.customer_name }}</td>

                                            <!-- Destination -->
                                            <td class="text-capitalize fw-16">{{ invoiceRow.store }}</td>

                                            <!-- sub order -->
                                            <td class="text-capitalize fw-16" v-if="invoiceRow.sub_order == 'xxx'">
                                                <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="6.5" cy="6.5" r="6.5" fill="#9E44F2"/>
                                                </svg>
                                                &nbsp;&nbsp;<span>{{ invoiceRow.sub_order }}</span>
                                            </td>
                                            <td class="text-capitalize fw-16" v-else>
                                                <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="6.5" cy="6.5" r="6.5" fill="#EF8F00"/>
                                                </svg>
                                                &nbsp;&nbsp;<span>{{ invoiceRow.sub_order }}</span>
                                            </td>

                                            <!-- Item -->
                                            <td class="text-capitalize fw-16">{{ invoiceRow.iteminfo }}</td>
                                            <!-- BarCode -->
                                            <td class="text-capitalize fw-16"><a href="javascript:;" class="text-decoration-none text-primary">{{ invoiceRow.barcode }}</a></td>
                                            <!-- Location -->
                                            <td>
                                                <div class="invoice-location assembling rounded-pill" :style="{'background-color': invoiceRow.location_color }">
                                                    <svg v-if="invoiceRow.process != 0" width="12" height="12" viewBox="0 0 12 12" :fill="'#'+invoiceRow.circle_color" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+invoiceRow.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+invoiceRow.circle_color" stroke-width="2"/>
                                                    </svg>
                                                    <svg v-else width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" :fill="'#'+invoiceRow.circle_color"/>
                                                        <circle cx="6" cy="6" r="5" :stroke="'#'+invoiceRow.circle_color" stroke-width="2"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<span class="d-block text-center text-nowrap" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                                                </div>
                                            </td>
                                            <!-- Prod -->
                                            <td class="text-capitalize fw-16">{{ invoiceRow.prod }}</td>
                                            <!-- Deliv -->
                                            <td class="text-capitalize fw-16 fw-bold">{{ invoiceRow.deliv }}</td>
                                        </tr>
                                    </transition-group>
                                </tbody>
                                <tfoot>
                                    <tr v-if="currentLoadedInvoiceCount != totalInvoiceCount">
                                        <td class="tcol" style="text-align: center" :colspan="Object.keys(columns).length"><button class="btn btn-link" @click="loadMoreInvoice">Show more ( {{ currentLoadedInvoiceCount }} / {{ totalInvoiceCount }})</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <router-view /> -->
</template>

<script>
    import {
        ASSEMBLY_HOME_MODULE,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
        RESET_ASSEMBLY_STATE,
        SET_ASSEMBLY_STATE,
        SET_INVOICE_LIST,
        GET_INVOICE_LIST,
        GET_TOTAL_INVOICE_COUNT,
        GET_LOADED_INVOICE_COUNT,
        LOAD_MORE_INVOICE,
        INVOICELIST_GET_CURRENT_SELECTED,
        INVOICELIST_GET_ALL_SELECTED,
        INVOICELIST_SET_CURRENT_SELECTED,
        INVOICELIST_SET_ALL_SELECTED,
        INVOICELIST_SET_MULTI_UNCHECKED,
        INVOICE_RESET_MULITCHECKED,
        ITEM_DETAIL_MODULE,
        ITEM_DETAIL_SET_DETAIL
    } from "../../store/types/types";
    import { useStore } from "vuex";
    import { useRoute, useRouter } from "vue-router";
    import { ref, onBeforeMount, onMounted, onUnmounted, computed } from "vue";
    import CheckBox from '../miscellaneous/CheckBox';
    export default {
        name: "AssemblyHome",
        components:{
            CheckBox
        },
        setup(){
            const store = useStore();
            const route = useRoute();
            const router = useRouter();
            const groupedPosteWidth = ref(0);
            const poste = ref("");
            const day = ref("");
            const type = ref("");
            const total_today = ref(0);
            const total_tommorow = ref(0);
            const linesPerPage = ref(["100","200","500","1000","2000","3000"]);
            const groupedPostes = ref([]);
            const mainStats = ref({});
            const assemblyStatsTotal = ref({});
            const assemblyStatsToday = ref({});
            const assemblyStatsTomorrow = ref({});
            const assemblyStatsOverdue = ref({});
            const assemblyStatsLater = ref({});
            const selectMode = ref('multi');
            const selectAll = ref(false);

            const columns = ref([
                    {
                        key: 'selected',
                    },
                    {
                        label: 'Order N&deg;',
                        key: 'item_id',
                        thClass: 'text-uppercase invoice-table-th',

                    },
                    {
                        label: 'Customer',
                        key: 'customer_name',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',
                    },
                    {
                        label: 'Destination',
                        key: 'store',
                        thClass: 'text-uppercase invoice-table-th',
                    },

                    {
                        label: 'Sub Order',
                        key: 'sub_order',
                        thClass: 'text-uppercase invoice-table-th',
                    },
                    {
                        label: 'Item',
                        key: 'iteminfo',
                        thClass: 'text-uppercase invoice-table-th',
                    },
                    {
                        label: 'Barcode',
                        key: 'barcode',
                        thClass: 'text-uppercase invoice-table-th',
                    },
                    {
                        label: 'Location',
                        key: 'location',
                        thClass: 'text-uppercase invoice-table-th',
                    },
                    {
                        label: 'Prod',
                        key: 'prod',
                        thClass: 'text-uppercase invoice-table-th',
                    },
                    {
                        label: 'Deliv',
                        key: 'deliv',
                        thClass: 'text-uppercase invoice-table-th',
                    },
            ]);
            onBeforeMount( () => {
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
                axios.post('/assembly-home-stats')
                    .then((res) => {
                        let gp = res.data.grouped_postes;
                        let width = 0;
                        if(parseInt(gp.length) > 0){
                            // width = 90/parseInt(gp.length);
                            groupedPosteWidth.value = 8.5;
                        }
                        groupedPostes.value = gp;
                        mainStats.value = res.data.main_stats;
                        assemblyStatsTotal.value = res.data.stats_total;
                        assemblyStatsToday.value = res.data.stats_today;
                        assemblyStatsTomorrow.value = res.data.stats_tomorrow;
                        assemblyStatsOverdue.value = res.data.stats_overdue;
                        assemblyStatsLater.value = res.data.stats_later;
                        store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_ASSEMBLY_STATE}`, res.data);
                    })
                    .catch(error => {
                        console.log(error);
                    }).finally(() => {
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
            });

            onMounted(() =>{

            })

            onUnmounted(()=>{
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${RESET_ASSEMBLY_STATE}`);
            })
            const invoiceList = computed(()=>{
                return store.getters[`${ASSEMBLY_HOME_MODULE}${GET_INVOICE_LIST}`];
            });
            const CURRENT_SELECTED=computed(()=>{
                return store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`];
            });
            const MULTI_SELECTED=computed(()=>{
                return store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_ALL_SELECTED}`];
            });
            const currentLoadedInvoiceCount = computed(()=>{
                return store.getters[`${ASSEMBLY_HOME_MODULE}${GET_LOADED_INVOICE_COUNT}`];
            });
            const totalInvoiceCount = computed(()=>{
                return store.getters[`${ASSEMBLY_HOME_MODULE}${GET_TOTAL_INVOICE_COUNT}`];
            })
            const getTableStats = (event, tmp_poste, tmp_day, tmp_type)=>{
                document.querySelectorAll('.each-poste-bloc').forEach((element)=>{
                    element.classList.remove('is_bloc_active');
                })
                event.target.closest('.each-poste-bloc').classList.add('is_bloc_active');
                getTable(tmp_poste, tmp_day, tmp_type);
            }
            const getTableByBloc = (event, tmp_poste, tmp_day, tmp_type)=>{
                let parentDIV = event.target.closest('.each-poste-bloc');
                if( ! parentDIV.classList.contains('is_bloc_disabled') ) {
                    document.querySelectorAll('.each-poste-bloc').forEach((element)=>{
                        element.classList.remove('is_bloc_active');
                    })
                    event.target.closest('.each-poste-bloc').classList.add('is_bloc_active');
                    getTable(tmp_poste, tmp_day, tmp_type);
                }
            }
            const getTable = (tmp_poste,tmp_day,tmp_type)=>{
                document.querySelector('#stats_table').classList.add('d-none');
                poste.value = tmp_poste;
                day.value = tmp_day;
                type.value = tmp_type;
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading Invoice Data...']);
                axios.post('/partner-details', {
                    poste:'',
                    typepost: tmp_poste,
                    day: tmp_day,
                    type: tmp_type,
                    skip: 0
                }).then((res) => {
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_INVOICE_LIST}`, res.data.data);
                }).catch((error) => {
                    console.log(error)
                }).finally(() => {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    document.querySelector('#stats_table').classList.remove('d-none');
                });
            }
            const loadMoreInvoice = ()=>{
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${LOAD_MORE_INVOICE}`, {
                    poste:'',
                    typepost: poste.value,
                    day: day.value,
                    type: type.value,
                    skip: currentLoadedInvoiceCount.value
                });
            }

            const checkboxclicked = ( check, id, name )=>{
                if(CURRENT_SELECTED.value == id && check == false){
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`,'');
                    store.commit(`${ITEM_DETAIL_MODULE}${ITEM_DETAIL_SET_DETAIL}`, {
                        breif_info: {
                            id: ''
                        },
                        location_history: []
                    });
                    router.back();
                }
                if(check == true){
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_ALL_SELECTED}`, id);
                }
                if(check==false){
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_MULTI_UNCHECKED}`, id);
                }
            }
            const checkboxallclicked = ( check, id, name )=>{
                if(check==false)
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICE_RESET_MULITCHECKED}`);
                if(check){
                    const list=_.cloneDeep(invoiceList.value);
                    list.forEach(item => store.dispatch(`${INVOICE_MODULE}${INVOICELIST_SET_ALL_SELECTED}`, item.item_id));
                }
            }
            const selectrow = (item_id)=>{
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${INVOICELIST_SET_CURRENT_SELECTED}`, item_id);
                router.push({
                    name:'ItemDetails',
                    params: {
                        item_id:item_id,
                    },
                })
            }
            return {
                groupedPosteWidth,
                poste,
                day,
                type,
                total_today,
                total_tommorow,
                linesPerPage,
                groupedPostes,
                mainStats,
                assemblyStatsTotal,
                assemblyStatsToday,
                assemblyStatsTomorrow,
                assemblyStatsOverdue,
                assemblyStatsLater,
                selectMode,
                invoiceList,
                selectAll,
                columns,
                getTableByBloc,
                getTableStats,
                getTable,
                loadMoreInvoice,
                checkboxclicked,
                checkboxallclicked,
                selectrow,
                CURRENT_SELECTED,
                MULTI_SELECTED,
                currentLoadedInvoiceCount,
                totalInvoiceCount,
                showlayer:computed(()=>{return (route.params.item_id>0&&store.getters[`${ASSEMBLY_HOME_MODULE}${INVOICELIST_GET_CURRENT_SELECTED}`]);})
            }
        }
    }
</script>

<style scoped>
*{
    font-family: 'Gotham Rounded Book';
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}
.w-13{
    width: 13% !important;
}
.w-87{
    width: 87% !important;
}
.bg-black{
    background-color: black;
}
.is-delivery-stores{
    font:normal 16px/1.3em "Gotham Rounded Book" !important;
}
.delivery_store a{
    font:normal 16px/1.3em "Gotham Rounded Book" !important;
}
#assembly-home .each-poste-bloc .each-poste-stats a{
    font-weight: bold;
}
.each-poste-label{
    font: normal 10px/1.3em 'Gotham Book' !important;
}
.invoice-table-th span{
    font-family: "Gotham Rounded";
    font-weight: 400;
    font-size: 14px;
    color: #868686;
}
.assembly-home-nav-item{
    border-radius: 10px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    background: #EEEEEE;
    cursor: pointer;
    font-family: 'Gotham Rounded Book';
    font-weight: 400;
}
.assembly-home-nav-item.active{
    color: #42A71E;
    font-weight: bold;
}
.invoice-location{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    padding: 5px 16px 5px 8px;
    font-family: 'Gotham Rounded Book';
    font-style: normal;
    font-weight: normal;
    font-size: 12px;
    line-height: 14px;
    background: rgba(241, 210, 164, 0.7);;
    color: #000000;
    max-width: 113px;
}
.on-van{
    background: rgba(234, 214, 247, 0.7);
    color: #9E44F2;
}
.storage{
    background: rgba(66, 167, 30, 0.2);
    color: #42A71E;
}
.assembling{
    background: rgba(212, 221, 247, 0.7);
    color: #4E58E7;
}
.visible-hidden .form-check{
    visibility : hidden;
}
.selected-row .visible-hidden .form-check,
tr:hover .visible-hidden .form-check{
    visibility : visible;
}
@media (min-width: 1600px) {
  .total_row {
    padding-left: 1rem !important;
  }
}
.trow{
    transition: background-color 300ms ease-out;
    cursor: pointer;
    display: table-row;
}
.trow:hover,
.trow.multi{
    transition: background-color 300ms ease-out;
    background: #F8F8F8;
}
.current_sel{
    position: relative;
    z-index:10000;
    background: rgb(247, 251, 246);
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
}
/*list transitions*/
.list-enter-from{
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to{
    opacity: 1;
    transform: scale(1);
}
.list-enter-active{
    transition: all 1s ease;
}

.list-leave-from{
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
}
</style>
<style>
    .trow span.chkbox {
        border: 2px solid #FFF;
        transition: border-color 300ms ease-out;
    }
    .trow:hover span.chkbox,
    .trow.multi span.chkbox {
        border-color: #868686;
        transition: border-color 300ms ease-out;
    }
</style>
