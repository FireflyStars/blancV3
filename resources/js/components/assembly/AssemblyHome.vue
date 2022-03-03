<template>
        <div class="w-100 position-relative bg-black">
            <div class="d-flex flex-wrap p-2">
                <div class="col-sm-6 col-md-4">
                    <div class="each-home-stats">
                        <div class="bg-white p-2 m-2 rounded-3">
                            <div class="d-flex mb-3">
                                <div class="col-2 as-stats-label fw-bold font-20 pl-4">Due<br/>Today</div>
                                <div class="col-2 px-2 text-end total_stats_num"><span>{{ mainStats.total_due_today ? mainStats.total_due_today : 0}}</span></div>
                                <div class="col-8 pr-5">
                                    <div class="red-stats text-white d-flex justify-content-between text-center rounded-pill">
                                        <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_today_deliveries<10?
                                        10:(mainStats.percent_today_deliveries>90?90:mainStats.percent_today_deliveries))+'%'}">
                                        {{ mainStats.due_today_deliveries ? parseInt(mainStats.due_today_deliveries) : 0}}</div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_today<10?
                                        10:(mainStats.percent_today_stores>90?90:mainStats.percent_today_stores))+'%'}">
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
                                        :style="{'width':+(mainStats.percent_today_inprocess_deliveries<10?
                                        10:(mainStats.percent_today_inprocess_deliveries>90?
                                        90:mainStats.percent_today_inprocess_deliveries))+'%'}" 
                                        :class="{'col-6':mainStats.due_today_deliveries==0,'px-0':mainStats.due_today_deliveries==0}">
                                        {{ mainStats.percent_today_inprocess_deliveries ? parseInt(mainStats.percent_today_inprocess_deliveries) : 0}}%
                                        </div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_today_done_deliveries<10?
                                        10:(mainStats.percent_today_done_deliveries>90?
                                        90:mainStats.percent_today_done_deliveries))+'%'}" 
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
                                        <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_today_inprocess_stores<10?
                                        10:(mainStats.percent_today_inprocess_stores>90?
                                        90:mainStats.percent_today_inprocess_stores))+'%'}" 
                                        :class="{'col-6':mainStats.due_today_stores==0,'px-0':mainStats.due_today_stores==0}">
                                        {{ mainStats.percent_today_inprocess_stores? parseInt(mainStats.percent_today_inprocess_stores) : 0}}%
                                        </div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_today_done_stores<10?
                                        10:(mainStats.percent_today_done_stores>90?
                                        90:mainStats.percent_today_done_stores))+'%'}" 
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
                                        <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_deliveries<10?
                                        10:(mainStats.percent_tomorrow_deliveries>90?90:mainStats.percent_tomorrow_deliveries))+'%'}">
                                        {{mainStats.due_tomorrow_deliveries ? mainStats.due_tomorrow_deliveries : 0 }}</div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_tomorrow<10?
                                        10:(mainStats.percent_tomorrow_stores>90?90:mainStats.percent_tomorrow_stores))+'%'}">
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
                                        :style="{'width':+(mainStats.percent_tomorrow_inprocess_deliveries<10?
                                        10:(mainStats.percent_tomorrow_inprocess_deliveries>90?
                                        90:mainStats.percent_tomorrow_inprocess_deliveries))+'%'}" 
                                        :class="{'col-6':mainStats.due_tomorrow_deliveries==0,'px-0':mainStats.due_tomorrow_deliveries==0}">
                                        {{mainStats.percent_tomorrow_inprocess_deliveries ? parseInt(mainStats.percent_tomorrow_inprocess_deliveries) : 0}}%
                                        </div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_done_deliveries<10?
                                        10:(mainStats.percent_tomorrow_done_deliveries>90?
                                        90:mainStats.percent_tomorrow_done_deliveries))+'%'}" 
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
                                        <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_inprocess_stores<10?
                                        10:(mainStats.percent_tomorrow_inprocess_stores>90?
                                        90:mainStats.percent_tomorrow_inprocess_stores))+'%'}" 
                                        :class="{'col-6':mainStats.due_tomorrow_stores==0,'px-0':mainStats.due_tomorrow_stores==0}">
                                        {{ mainStats.percent_tomorrow_inprocess_stores ? parseInt(mainStats.percent_tomorrow_inprocess_stores) : 0}}%
                                        </div>
                                        <div class="py-1 px-2" :style="{'width':+(mainStats.percent_tomorrow_done_stores<10?
                                        10:(mainStats.percent_tomorrow_done_stores>90?
                                        90:mainStats.percent_tomorrow_done_stores))+'%'}" 
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
                            <div class="col-2 as-stats-label fw-bold font-20 pl-4">Due<br/>Day after</div>
                            <div class="col-2 px-2 text-end total_stats_num">{{mainStats.total_due_later ? mainStats.total_due_later : 0}}</div>
                            <div class="col-8 pr-5">
                                <div class="red-stats text-white d-flex justify-content-between text-center">
                                    <div class="standard-bar py-1 px-2" 
                                        :style="{'width':+ ( mainStats.percent_later_deliveries < 10 ? 10: ( mainStats.percent_later_deliveries>90?90:mainStats.percent_later_deliveries))+'%'}">
                                    {{mainStats.due_later_deliveries ? mainStats.due_later_deliveries : 0 }}</div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_exp_later<10?
                                    10:(mainStats.percent_later_stores>90?90:mainStats.percent_later_stores))+'%'}">
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
                                    :style="{'width':+(mainStats.percent_later_inprocess_deliveries<10?
                                    10:(mainStats.percent_later_inprocess_deliveries>90?
                                    90:mainStats.percent_later_inprocess_deliveries))+'%'}" 
                                    :class="{'col-6':mainStats.due_later_deliveries==0,'px-0':mainStats.due_later_deliveries==0}">
                                    {{mainStats.percent_later_inprocess_deliveries ? parseInt(mainStats.percent_later_inprocess_deliveries) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_later_done_deliveries<10?
                                    10:(mainStats.percent_later_done_deliveries>90?
                                    90:mainStats.percent_later_done_deliveries))+'%'}" 
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
                                    <div class="standard-bar py-1 px-2" :style="{'width':+(mainStats.percent_later_inprocess_stores<10?
                                    10:(mainStats.percent_later_inprocess_stores>90?
                                    90:mainStats.percent_later_inprocess_stores))+'%'}" 
                                    :class="{'col-6':mainStats.due_later_stores==0,'px-0':mainStats.due_later_stores==0}">
                                    {{ mainStats.percent_later_inprocess_stores ? parseInt(mainStats.percent_later_inprocess_stores) : 0}}%
                                    </div>
                                    <div class="py-1 px-2" :style="{'width':+(mainStats.percent_later_done_stores<10?
                                    10:(mainStats.percent_later_done_stores>90?
                                    90:mainStats.percent_later_done_stores))+'%'}" 
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
                            <div class="d-flex mb-1">
                                <div class="col-2"><h2 id="order_status_heading" class="font-22 mt-3 mb-0 mx-0">Items in <br>Production</h2></div>
                                <div class="col-10 mt-auto">
                                    <div class="d-flex justify-content-between">
                                        <div v-for="(a, index) in groupedPostes" :style="{width: parseInt(90/groupedPostes.length)+'%'}" class="text-center each-poste-label" :key="index">
                                            <span class="text-capitalize" v-if="a.group_name=='Partner'">With<br/></span>
                                            <span class="text-capitalize" v-else-if="a.group_name!='Storage'&&a.group_name!='Conveyor'">In<br/></span>
                                            <span class="text-capitalize" v-if="a.group_name!='QC 1' && a.group_name!='QC 2'"></span>
                                            <span class="text-capitalize" v-else>&nbsp;</span>
                                            <span class="text-capitalize" v-if="a.group_name=='Conveyor'">On Assembly Conveyor</span>
                                            <span class="text-capitalize" v-else-if="a.group_name=='Storage'">On Storage Conveyor</span>
                                            <span class="text-capitalize" v-else>{{ a.formatted_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Row today-->
                            <div class="d-flex align-items-center">
                                <div class="col-2"></div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between total_row">
                                        <div v-for="(a, index) in groupedPostes" :style="{width:groupedPosteWidth+'%'}" class="text-center" :key="index">
                                            <span class="w-100 each-poste-stats ps-xl-2 pe-xl-2">{{assemblyStatsTotal[a.group_name]}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center d-none">
                                <div class="col-2"><span class="fst-italic">Avg time</span></div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between total_row">
                                        <div v-for="(a, index) in groupedPostes" :style="{width:groupedPosteWidth+'%'}" class="text-center" :key="index">
                                            <span class=""> 4 hr </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-2">
                                    <div class="w-100 standard-label fw-bold">Due<br/>Today</div>
                                    <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                                </div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between">
                                        <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsToday[a.group_name].std === 0 && assemblyStatsToday[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'today','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                            <div class="w-100 text-center each-poste-stats poste-stats-top grey-bg py-1">
                                                <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','all')">{{assemblyStatsToday[a.group_name].all}}</a>
                                            </div>
                                            <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
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
                                <div class="col-2">
                                    <div class="w-100 standard-label pb-1 fw-bold">Due<br/>Tomorrow</div>
                                    <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                                </div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between">
                                        <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsTomorrow[a.group_name].std === 0 && assemblyStatsTomorrow[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'tomorrow','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">

                                            <div class="w-100 text-center each-poste-stats poste-stats-top grey-bg py-1">
                                                <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','all')">{{assemblyStatsTomorrow[a.group_name].all}}</a>
                                            </div>
                                            <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
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
                                <div class="col-2">
                                    <div class="w-100 standard-label pb-1 fw-bold">All<br/>Overdues</div>
                                    <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                                </div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between">
                                        <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsOverdue[a.group_name].std === 0 && assemblyStatsOverdue[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'overdue','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                            <div class="w-100 text-center each-poste-stats poste-stats-top grey-bg py-1">
                                                <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','all')">{{assemblyStatsOverdue[a.group_name].all}}</a>
                                            </div>
                                            <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
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
                                <div class="col-2">
                                    <div class="w-100 standard-label pb-1 fw-bold">Due<br/>Later</div>
                                    <div class="w-100 standard-label is-delivery-stores">Delivery<br/>Stores</div>
                                </div>
                                <div class="col-10">
                                    <div class="d-flex justify-content-between">
                                        <div v-for="(a, index) in groupedPostes" class="px-0 each-poste-bloc" :class="{'is_bloc_disabled':assemblyStatsLater[a.group_name].std === 0 && assemblyStatsLater[a.group_name].exp === 0}" @click="getTableByBloc($event,a.group_name,'later','all')" :style="{width:groupedPosteWidth+'%'}" :key="index">
                                            <div class="w-100 text-center each-poste-stats poste-stats-top grey-bg py-1">
                                                <a class="" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','all')">{{assemblyStatsLater[a.group_name].all}}</a>
                                            </div>
                                            <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
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
                                            <th v-for="(item, index) in columns" :class="item.thClass" :key="index" v-html="item.label ? item.label : ''" style="border-bottom: 2px solid #dee2e6 !important"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(invoiceRow, index) in invoiceList" :key="index" @click="onRowSelected($event, invoiceRow.order_id)">
                                            <td class="visible-hidden">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" :id="'invoice-'+invoiceRow.order_id">
                                                    <label class="form-check-label" :for="'invoice-'+invoiceRow.order_id"></label>
                                                </div>
                                            </td>
                                            
                                            <td class="text-capitalize fw-16" v-if="invoiceRow.order_id == 'xxx'">
                                                <span>{{ invoiceRow.order_id }}</span>&nbsp;&nbsp;
                                                <svg width="32" height="32" viewBox="0 0 44 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.7344 16.763V23.0131C20.7344 23.2514 20.8394 23.474 21.0208 23.6225L24.8403 26.7475L25.7952 25.5268L22.2621 22.6381V16.7631L20.7344 16.763Z" fill="#EB5757"/>
                                                    <path d="M30.9284 22.5C30.9284 27.5601 26.9204 31.6429 21.9999 31.6429C17.0793 31.6429 13.0713 27.5601 13.0713 22.5C13.0713 17.4399 17.0793 13.3571 21.9999 13.3571C26.9204 13.3571 30.9284 17.4399 30.9284 22.5Z" stroke="#EB5757"/>
                                                </svg>
                                            </td>
                                            <td class="text-capitalize fw-16" v-else><span>{{ invoiceRow.order_id }}</span></td>

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
                                                <div class="invoice-location assembling rounded-pill" :style="{'background-color': '#'+invoiceRow.location_color }">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" fill="#4E58E7"/>
                                                    <circle cx="6" cy="6" r="5" stroke="#4E58E7" stroke-width="2"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                                                </div>
                                                <!-- <div v-if="invoiceRow.location == 'On Van'" class="invoice-location on-van rounded-pill">
                                                    {{ invoiceRow.location }}
                                                </div>
                                                <div v-else-if="invoiceRow.location == 'Storage'" class="invoice-location storage rounded-pill">
                                                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="6" cy="6.5" r="5" stroke="#42A71E" stroke-width="2"/>
                                                    </svg>&nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                                                </div>
                                                <div v-else-if="invoiceRow.location == 'Assembling'" class="invoice-location assembling rounded-pill">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" fill="#4E58E7"/>
                                                    <circle cx="6" cy="6" r="5" stroke="#4E58E7" stroke-width="2"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                                                </div>
                                                <div v-else class="invoice-location rounded-pill">
                                                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_10_4466)">
                                                        <path d="M10.9318 6.73315H1.35156C1.35156 8.56699 2.26215 12.1588 5.90449 11.8552C9.54684 11.5517 10.7737 8.31405 10.9318 6.73315Z" fill="#EF8F00"/>
                                                        <circle cx="6" cy="6.5" r="5" stroke="#EF8F00" stroke-width="2"/>
                                                        </g>
                                                        <defs>
                                                        <clipPath id="clip0_10_4466">
                                                        <rect width="12" height="12" fill="white" transform="translate(0 0.5)"/>
                                                        </clipPath>
                                                        </defs>
                                                    </svg>
                                                    &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ invoiceRow.location }}</span>
                                                </div> -->
                                            </td>
                                            <!-- Prod -->
                                            <td class="text-capitalize fw-16">{{ invoiceRow.prod }}</td>
                                            <!-- Deliv -->
                                            <td class="text-capitalize fw-16 fw-bold">{{ invoiceRow.deliv }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    import {
        ASSEMBLY_HOME_MODULE,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
        RESET_ASSEMBLY_STATE,
        SET_ASSEMBLY_STATE,
        SET_ASSEMBLY_INVOICE,
    } from "../../store/types/types";
    import { useStore } from "vuex";    
    import { ref, onBeforeMount, onMounted, onUnmounted } from "vue";
    export default {
        name: "AssemblyHome",
        components:{

        },
        setup(){
            const store = useStore();
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
            const numberoflines = ref(0);
            const selectMode = ref('multi');
            const selectAll = ref(false);
            const invoiceList = ref([
                    // { order_id: 1575, customer_name: 'Gras Marion', store: 'Delivery', sub_order: '02-002018', iteminfo: 'Down-Filled Coat', barcode: '02224123', location: 'On Van', prod: '24/02', deliv: '24/02' },
                    // { order_id: 1576, customer_name: 'Eva Spaeter', store: 'Delivery', sub_order: '02-002015', iteminfo: 'Cushion cover, Small', barcode: '02224123', location: 'On Van', prod: '24/02', deliv: '24/02' },
                    // { order_id: 1577, customer_name: 'James Morres', store: 'Store', sub_order: '02-002017', iteminfo: 'Evening Dress', barcode: '02224145', location: 'Storage', prod: '24/02', deliv: '24/02' },
                    // { order_id: 1578, customer_name: 'Soumya Jaga', store: 'Store', sub_order: '02-002019', iteminfo: 'Jeans', barcode: '02224146', location: 'Assembling', prod: '24/02', deliv: '24/02' },
                    // { order_id: 1579, customer_name: 'Jaga Pala', store: 'Store', sub_order: '02-002333', iteminfo: 'Shorts', barcode: '02224148', location: 'Cleaning', prod: '24/02', deliv: '24/02' },
            ]);
            const columns = ref([
                    {
                        key: 'selected',
                        tdClass: 'visible-hidden'
                    },
                    {
                        label: 'Order N&deg;',
                        key: 'order_id',
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
                // store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
                // axios.post('/assembly-home-stats')
                //     .then((res) => {
                //         let gp = res.data.grouped_postes;
                //         let width = 0;
                //         if(parseInt(gp.length) > 0){
                //             width = 80/parseInt(gp.length);
                //             groupedPosteWidth.value = width;
                //         }
                //         groupedPostes.value = gp;
                //         mainStats.value = res.data.main_stats;
                //         assemblyStatsTotal.value = res.data.stats_total;
                //         assemblyStatsToday.value = res.data.stats_today;
                //         assemblyStatsTomorrow.value = res.data.stats_tomorrow;
                //         assemblyStatsOverdue.value = res.data.stats_overdue;
                //         assemblyStatsLater.value = res.data.stats_later;
                //         store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_ASSEMBLY_STATE}`, res.data);
                //     })
                //     .catch(error => {
                //         console.log(error);
                //     }).finally(() => {
                //         store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                //     });                
            });

            onMounted(() =>{
                
            })

            onUnmounted(()=>{
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${RESET_ASSEMBLY_STATE}`);
            })

            function onRowSelected(event, order_id){
                let selectedRowCheckbox = document.querySelector('#invoice-' + order_id);
                event.target.parentElement.classList.toggle('selected-row');

                if(selectedRowCheckbox.hasAttribute('checked')){
                    selectedRowCheckbox.removeAttribute('checked');
                }else{
                    selectedRowCheckbox.setAttribute('checked', true);
                }
            }
            function getTableStats(event, tmp_poste, tmp_day, tmp_type){
                document.querySelectorAll('.each-poste-bloc').forEach((element)=>{
                    element.classList.remove('is_bloc_active');
                })
                event.target.closest('.each-poste-bloc').classList.add('is_bloc_active');
                getTable(tmp_poste, tmp_day, tmp_type);
            }
            function getTableByBloc(event, tmp_poste, tmp_day, tmp_type){
                let parentDIV = event.target.closest('.each-poste-bloc');
                if( ! parentDIV.classList.contains('is_bloc_disabled') ) {
                    document.querySelectorAll('.each-poste-bloc').forEach((element)=>{
                        element.classList.remove('is_bloc_active');
                    })                    
                    event.target.closest('.each-poste-bloc').classList.add('is_bloc_active');
                    getTable(tmp_poste, tmp_day, tmp_type);
                }
            }
            function getTable(tmp_poste,tmp_day,tmp_type){
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
                    // tableprops: JSON.stringify(this.$refs.partnerstable.tableProps),
                }).then((res) => {
                    invoiceList.value  = res.data.data;
                    numberoflines.value = res.data.count_data;
                    store.dispatch(`${ASSEMBLY_HOME_MODULE}${SET_ASSEMBLY_INVOICE}`, res.data.data);
                }).catch((error) => {
                    invoiceList.value = [];
                    console.log(error)
                }).finally(() => {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    document.querySelector('#stats_table').classList.remove('d-none');
                });
            }
            const filterDef={
                def:{
                    'infoOrder.paid':{
                        name:"Payment status",
                        options:{
                            0:"Unpaid",
                            1:"Paid",
                        }
                    },
                    'infoOrder.Status':{
                        name:"Order status",
                        options:{
                            'ASSEMBLING':'ASSEMBLING',
                            'AWAITING REDELIVERY':'AWAITING REDELIVERY',
                            'AWAITING SALE':'AWAITING SALE',
                            'CANCELLED':'CANCELLED',
                            'CHECK IN ATELIER':'CHECK IN ATELIER',
                            'COLLECTED':'COLLECTED',
                            'DELETE':'DELETE',
                            'DELIVERED':'DELIVERED',
                            'DELIVERED TO STORE':'DELIVERED TO STORE',
                            'DELIVERY IN STORE':'DELIVERY IN STORE',
                            'DONATED TO CHARITY':'DONATED TO CHARITY',
                            'DROPPED OFF':'DROPPED OFF',
                            'FAILED DELIVERY':'FAILED DELIVERY',
                            'FAILED PAYMENT':'FAILED PAYMENT',
                            'FULFILLED':'FULFILLED',
                            'IN PROCESS':'IN PROCESS',
                            'IN STORAGE':'IN STORAGE',
                            'LATE':'LATE',
                            'LATE DELIVERY':'LATE DELIVERY',
                            'MISSED PICKUP':'MISSED PICKUP',
                            'OFFLOADED':'OFFLOADED',
                            'ON VAN':'ON VAN',
                            'OVERDUE FOR COLLECTION':'OVERDUE FOR COLLECTION',
                            'OVERDUE STORE':'OVERDUE STORE',
                            'PART ON HOLD':'PART ON HOLD',
                            'PART PENDING':'PART PENDING',
                            'PICKED UP':'PICKED UP',
                            'READY':'READY',
                            'RECURRING':'RECURRING',
                            'READY IN STORE':'READY IN STORE',
                            'SCHEDULED':'SCHEDULED',
                            'SOLD':'SOLD',
                            'VOID':'VOID',

                        }
                    },
                    'infoCustomer.TypeDelivery':{
                        name:"Destination",
                        options:{
                            'DELIVERY':'DELIVERY',
                            'CHELSEA':'CHELSEA',
                            'MARYLEBONE':'MARYLEBONE',
                            'NOTTING HILL':'NOTTING HILL',
                            'SOUTH KEN':'SOUTH KEN'
                        }

                    },
                    'infoitems.express':{
                        name: "Turnaround time",
                        options:{
                            standard:"Standard",
                            exp24:"Express 24h",
                            exp48:"Express 48h"
                        }

                    }
                }
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
                numberoflines,
                selectMode,
                invoiceList,
                selectAll,
                columns,
                onRowSelected,
                getTableByBloc,
                getTableStats,
                getTable,
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
.is-delivery-stores{
    font:normal 16px/1.3em "Gotham Rounded Book"!important;
}
.delivery_store a{
    font:normal 16px/1.3em "Gotham Rounded Book"!important;
}
.delivery_store{
    padding-top:10px!important
}
.invoice-table-th{
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
</style>
