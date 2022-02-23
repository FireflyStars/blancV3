<template>
    <transition enter-active-class="animate__animated animate__fadeIn">
        <div class="container-fluid h-100 bg-color">
            <main-header></main-header>
            <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax" style="z-index: 100">
                <side-bar></side-bar>
                <div class="col main-view mx-5 py-5" id="assembly-home">
                    <h2 class="mx-0 mb-3">Production Track</h2>
                    <div class="nav-panel d-flex justify-content-between mb-1">
                        <ul class="assembly-home-nav list-inline mb-0">
                            <li class="assembly-home-nav-item fw-bold list-inline-item px-3 py-2" :class="selected_nav == 'Stations' ? 'bg-white' : ''" @click="selected_nav = 'Stations'">Stations</li>
                            <li class="assembly-home-nav-item fw-bold list-inline-item px-3 py-2" :class="selected_nav == 'Commitment' ? 'bg-white' : ''" @click="selected_nav = 'Commitment'">Commitment</li>
                            <li class="assembly-home-nav-item fw-bold list-inline-item px-3 py-2" :class="selected_nav == 'All items' ? 'bg-white' : ''" @click="selected_nav = 'All items'">All items</li>
                            <li class="assembly-home-nav-item fw-bold list-inline-item px-3 py-2" :class="selected_nav == 'Overdue' ? 'bg-white' : ''" @click="selected_nav = 'Overdue'">Overdue</li>
                        </ul>
                        <button class="filter-btn d-flex align-items-center justify-content-between p-2 rounded-3 fw-bold">
                            Filter &nbsp;&nbsp;&nbsp;&nbsp;
                            <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect y="0.941162" width="21" height="2.41033" rx="1.20516" fill="#47454B"/>
                            <rect x="3.66309" y="6.96704" width="13.6744" height="2.41033" rx="1.20516" fill="#47454B"/>
                            <rect x="7.3252" y="12.9928" width="6.34883" height="2.41033" rx="1.20517" fill="#47454B"/>
                            </svg>
                        </button>
                    </div>
                    <div class="w-100 position-relative bg-black">
                        <div class="d-flex flex-wrap p-2">
                            <div class="col-sm-6 col-md-4">
                                <div class="each-home-stats">
                                    <div class="bg-white p-2 m-2 rounded-3">
                                        <div class="d-flex mb-3">
                                            <div class="col-2 as-stats-label pl-4">Due<br/>Today</div>
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
                                            <div class="col-2 as-stats-label pl-4">Stores</div>
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
                                            <div class="col-2 as-stats-label pl-4">Due<br/>Tomorrow</div>
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
                                        <div class="col-2 as-stats-label pl-4">Due<br/>Day after</div>
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
                        </div>
                    </div>
                    <div class="w-100 d-flex bg-black" id="assembly-home-stats">
                        <div class="col-12 p-0">
                            <div class="col-12 p-0">
                                <div v-if="groupedPostes">
                                    <div class="bg-white mb-3 p-3">
                                        <div class="d-flex mb-1">
                                            <div class="col-2"><h2 id="order_status_heading" class="mt-3 mb-0 mx-0">Items in <br>Production</h2></div>
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
                                        <div class="d-flex align-items-center">
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
                                                            <a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','all')">{{assemblyStatsToday[a.group_name].all}}</a>
                                                        </div>
                                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','delivery')">{{assemblyStatsToday[a.group_name].delivery}}</a></p>
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'today','store')">{{assemblyStatsToday[a.group_name].store}}</a></p>
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
                                                            <a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','all')">{{assemblyStatsTomorrow[a.group_name].all}}</a>
                                                        </div>
                                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','delivery')">{{assemblyStatsTomorrow[a.group_name].delivery}}</a></p>
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'tomorrow','store')">{{assemblyStatsTomorrow[a.group_name].store}}</a></p>
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
                                                            <a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','all')">{{assemblyStatsOverdue[a.group_name].all}}</a>
                                                        </div>
                                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','delivery')">{{assemblyStatsOverdue[a.group_name].delivery}}</a></p>
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'overdue','store')">{{assemblyStatsOverdue[a.group_name].store}}</a></p>
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
                                                            <a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','all')">{{assemblyStatsLater[a.group_name].all}}</a>
                                                        </div>
                                                        <div class="w-100 text-center each-poste-stats poste-stats-bottom grey-bg py-1 delivery_store" >
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','delivery')">{{assemblyStatsLater[a.group_name].delivery}}</a></p>
                                                            <p class="mb-0"><a class="text-decoration-none" href="javascript:void(0)" @click.stop="getTableStats($event,a.group_name,'later','store')">{{assemblyStatsLater[a.group_name].store}}</a></p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex bg-white mb-3" id="stats_table">
                                        <div class="text-white col-12 dttable" id="invoice-list-main">
                                            <b-table 
                                                hover 
                                                :items="items"
                                                :fields="columns" 
                                                ref="invoiceList" 
                                                selectable 
                                                :select-mode="selectMode" 
                                                class="mb-0"
                                                @row-selected="onRowSelected"
                                                > 
                                                <template #table-busy>
                                                    <div class="text-center text-success my-2">
                                                        <b-spinner class="align-middle"></b-spinner>
                                                        <strong>Loading...</strong>
                                                    </div>
                                                </template>
                                                <!-- <template #head(selected)="data">
                                                        <b-form-checkbox
                                                            id="select-all-item"
                                                            v-model="selectAll"
                                                            name="select-all"
                                                            value="all"
                                                            unchecked-value="reset"
                                                            @click="selectAll == 'all' ? data.selectAllRows : data.clearSelected">
                                                        </b-form-checkbox>
                                                </template>                                                 -->

                                                <template #cell(selected)="data">
                                                    <!-- <b-form-checkbox
                                                        :id="('invoice-' + data.index)"
                                                        value="1"
                                                        unchecked-value="0"
                                                        class="invoice-item-select-checkbox"
                                                    >
                                                    </b-form-checkbox> -->
                                                </template>                                    
                                                <template #cell(order_id)="data">
                                                    <template v-if="data.value == 1575">
                                                        <span>{{ data.value }}</span>
                                                    </template>
                                                    <template v-else>
                                                        <span>{{ data.value }}</span>&nbsp;&nbsp;
                                                        <svg width="32" height="32" viewBox="0 0 44 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M20.7344 16.763V23.0131C20.7344 23.2514 20.8394 23.474 21.0208 23.6225L24.8403 26.7475L25.7952 25.5268L22.2621 22.6381V16.7631L20.7344 16.763Z" fill="#EB5757"/>
                                                            <path d="M30.9284 22.5C30.9284 27.5601 26.9204 31.6429 21.9999 31.6429C17.0793 31.6429 13.0713 27.5601 13.0713 22.5C13.0713 17.4399 17.0793 13.3571 21.9999 13.3571C26.9204 13.3571 30.9284 17.4399 30.9284 22.5Z" stroke="#EB5757"/>
                                                        </svg>
                                                    </template>
                                                </template>                      
                                                <template #cell(sub_order)="data">
                                                    <template v-if="data.value == '02-002018'">
                                                        <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="6.5" cy="6.5" r="6.5" fill="#9E44F2"/>
                                                        </svg>
                                                        &nbsp;&nbsp;<span>{{ data.value }}</span>
                                                    </template>
                                                    <template v-else>
                                                        <svg width="11" height="11" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="6.5" cy="6.5" r="6.5" fill="#EF8F00"/>
                                                        </svg>
                                                        &nbsp;&nbsp;<span>{{ data.value }}</span>
                                                    </template>
                                                </template>    
                                                <template #cell(barcode)="data">
                                                    <a href="javascript:;" class="text-primary text-decoration-none">{{ data.value }}</a>
                                                </template>
                                                <template #cell(location)="data">
                                                    <template v-if="data.value == 'On Van'">
                                                        <div class="invoice-location on-van rounded-pill">
                                                            {{ data.value }}
                                                        </div>
                                                    </template>
                                                    <template v-else-if="data.value == 'Storage'">
                                                        <div class="invoice-location storage rounded-pill">
                                                            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="6" cy="6.5" r="5" stroke="#42A71E" stroke-width="2"/>
                                                            </svg>&nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ data.value }}</span>
                                                        </div>
                                                    </template>
                                                    <template v-else-if="data.value == 'Assembling'">
                                                        <div class="invoice-location assembling rounded-pill">
                                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.9318 6.23315H1.35156C1.35156 8.06699 2.26215 11.6588 5.90449 11.3552C9.54684 11.0517 10.7737 7.81405 10.9318 6.23315Z" fill="#4E58E7"/>
                                                            <circle cx="6" cy="6" r="5" stroke="#4E58E7" stroke-width="2"/>
                                                            </svg>
                                                            &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ data.value }}</span>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="invoice-location rounded-pill">
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
                                                            &nbsp;&nbsp;<span class="d-block text-center" :style="{ width: 'calc( 100% - 12px )'}">{{ data.value }}</span>
                                                        </div>
                                                    </template>
                                                </template>
                                            </b-table>
                                            
                                            <!-- <template>
                                                <data-table
                                                        :data="data"
                                                        :columns="columns" ref="partnerstable" class="is-bottom-table"
                                                >
                                                    <div slot="filters" slot-scope="{ tableData, perPage }">
                                                        <div class="row mt-4">
                                                            <div class="col-md-3">

                                                                <select class="form-control" v-model="tableData.length" @change="changeNbLines($event)">
                                                                    <option :key="page" v-for="page in linesPerPage">{{ page }}</option>
                                                                </select> 
                                                            
                                                            </div>

                                                            <div class="col-md-9">
                                                                <b style="color:#232323" v-if="numberoflines!=null">{{numberoflines}} <span v-if="numberoflines==1">item</span><span v-else>items</span></b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <tbody slot="body" slot-scope="{ data }">
                                                    <tr
                                                            :key="item.id"
                                                            v-for="item in data" class="laravel-vue-datatable-tbody-tr">
                                                        <td
                                                                :key="column.name"
                                                                v-for="column in columns">
                                                            <data-table-cell
                                                                    :value="item"
                                                                    :name="column.name"
                                                                    :meta="column.meta"
                                                                    :comp="column.component"
                                                                    :classes="column.classes">
                                                            </data-table-cell>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </data-table>
                                            </template> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import SideBar from "../layout/SideBar";
    import MainHeader from "../layout/MainHeader";
    // import SingleRowBarcode from '../datatable/SingleRowBarcode';
    import {
        ASSEMBLY_HOME_MODULE,
        LOADER_MODULE,
        DISPLAY_LOADER,
        HIDE_LOADER,
        RESET_ASSEMBLY_STATE,
    } from "../../store/types/types";
    import { useStore } from "vuex";    
    import { ref, onBeforeMount, onMounted, onUnmounted } from "vue";
    export default {
        name: "AssemblyHome",
        components:{
            SideBar, 
            MainHeader
        },
        setup(){
            const store = useStore();
            const groupedPosteWidth = ref(0);
            const selected_nav = ref('Stations');
            // const data = ref();
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
            // const onRowSelected = ()=>{
            //     alert('ok');
            // }
            onBeforeMount( () => {
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
                axios.post('/assembly-home-stats')
                    .then((res) => {
                        let gp = res.data.grouped_postes;
                        let width = 0;
                        if(parseInt(gp.length) > 0){
                            width = 80/parseInt(gp.length);
                            groupedPosteWidth.value = width;
                        }
                        groupedPostes.value = gp;
                        mainStats.value = res.data.main_stats;
                        assemblyStatsTotal.value = res.data.stats_total;
                        assemblyStatsToday.value = res.data.stats_today;
                        assemblyStatsTomorrow.value = res.data.stats_tomorrow;
                        assemblyStatsOverdue.value = res.data.stats_overdue;
                        assemblyStatsLater.value = res.data.stats_later;
                    })
                    .catch(error => {
                        console.log(error);
                    }).finally(() => {
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });                
            });
            // const setBlocHeight = function(){
            //     let doc_height = $(document).height();
            //     let bloc = $('#assembly-home-stats');
            //     let win_height = $(window).height();
            //     bloc.height(doc_height);
            // };
            // const partnerstable = ref(null);
            onMounted(() =>{
                // $('#invoice-list-main').hide();
                // this.$refs.partnerstable.tableProps.column = 'invoice';
                // this.$refs.partnerstable.tableProps.dir = 'desc';
                // this.$refs.partnerstable.tableProps.length = 100;
                // $('.table-header-sorting').click(function(){
                //     cmp.getTable(cmp.poste,cmp.day,cmp.type);
                // });
                // $('body').on('click','#partner-stats',function(event){
                //     let el = $(event.target);
                //     let parent_div = el.closest('.each-poste-bloc');
                //     if(parent_div.length===0){
                //         $('#invoice-list-main').hide();
                //         cmp.data = {};
                //     }

                // });
                // setBlocHeight();
            })

            onUnmounted(()=>{
                store.dispatch(`${ASSEMBLY_HOME_MODULE}${RESET_ASSEMBLY_STATE}`);
            })
            return {
                groupedPosteWidth,
                // data,
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
                selected_nav,
                selectMode,
                // isBusy,
                selectAll,
                // onRowSelected
            }
        },
        data:function(){
            return {
                items: [
                    { order_id: 1575, customer_name: 'Gras Marion', store: 'Delivery', sub_order: '02-002018', iteminfo: 'Down-Filled Coat', barcode: '02224123', location: 'On Van', prod: '24/02', deliv: '24/02' },
                    { order_id: 1576, customer_name: 'Eva Spaeter', store: 'Delivery', sub_order: '02-002015', iteminfo: 'Cushion cover, Small', barcode: '02224123', location: 'On Van', prod: '24/02', deliv: '24/02' },
                    { order_id: 1577, customer_name: 'James Morres', store: 'Store', sub_order: '02-002017', iteminfo: 'Evening Dress', barcode: '02224145', location: 'Storage', prod: '24/02', deliv: '24/02' },
                    { order_id: 1578, customer_name: 'Soumya Jaga', store: 'Store', sub_order: '02-002019', iteminfo: 'Jeans', barcode: '02224146', location: 'Assembling', prod: '24/02', deliv: '24/02' },
                    { order_id: 1579, customer_name: 'Jaga Pala', store: 'Store', sub_order: '02-002333', iteminfo: 'Shorts', barcode: '02224148', location: 'Cleaning', prod: '24/02', deliv: '24/02' },
                ],                
                columns: [
                    {
                        key: 'selected',
                        tdClass: 'visible-hidden'
                    },
                    {
                        label: 'Order N',
                        key: 'order_id',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',

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
                        tdClass: 'text-capitalize fw-16',                        
                    },

                    {
                        label: 'Sub Order',
                        key: 'sub_order',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',                        
                    },
                    {
                        label: 'Item',
                        key: 'iteminfo',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',                        
                    },
                    {
                        label: 'Barcode',
                        key: 'barcode',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',                        
                    },
                    {
                        label: 'Location',
                        key: 'location',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',                        
                    },
                    {
                        label: 'Prod',
                        key: 'prod',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16',                        
                    },
                    {
                        label: 'Deliv',
                        key: 'deliv',
                        thClass: 'text-uppercase invoice-table-th',
                        tdClass: 'text-capitalize fw-16 fw-bold',                        
                    },
                ]
            }
        },
        filters:{
            formatName(value){
                return value.replace(' Partner','');
            },
        },
        methods:{
            getTableStats(event,poste,day,type){
                $('body').find('.each-poste-bloc').removeClass('is_bloc_active');
                let el = $(event.target);
                el.closest('.each-poste-bloc').addClass('is_bloc_active');
                this.getTable(poste,day,type);
            },
            getTableByBloc(event,poste,day,type){

                let el = $(event.target);
                let parent_div = el.closest('.each-poste-bloc');

                if(!parent_div.hasClass('is_bloc_disabled')) {
                    $('body').find('.each-poste-bloc').removeClass('is_bloc_active');

                    el.closest('.each-poste-bloc').addClass('is_bloc_active');
                    this.getTable(poste, day, type);
                }
            },
            getTable(poste,day,type){
                this.poste = poste;
                this.day = day;
                this.type = type;
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading Partner Data...']);
                // $('#invoice-list-main').hide();
                // this.showLoader = true;
                axios.post('/partner-details', {
                    poste:'',
                    typepost:poste,
                    day:day,
                    type:type,
                    tableprops:JSON.stringify(this.$refs.partnerstable.tableProps),
                }).then((res) => {

                    // $('#invoice-list-main').show();
                    this.items = res.data;
                    this.numberoflines=res.data.count_data;
                    this.$store.dispatch('setAssemblyHomeStats');
                }).catch((error) => {
                }).finally(() => {
                    // always executed
                    // this.showLoader = false;
                    // this.$store.dispatch('setAssemblyHomeStats');

                //     let nav_height = $('nav').height();
                //     let div_pos = $("body").find("#stats_table").offset().top;
                //    $('html, body').animate({
                //         scrollTop: div_pos - nav_height
                //     }, 2000);
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            },
            changeNbLines(event){
                let nb_lines = event.target.value;
                this.$refs.partnerstable.tableProps.length = nb_lines;
                this.getTable(this.poste,this.day,this.type);
            },
        },
    }
</script>

<style scoped>
.is-delivery-stores{
    font:normal 16px/1.3em "Gotham Book"!important;
}
.delivery_store a{
    font:normal 16px/1.3em "Gotham Book"!important;
}
.delivery_store{
    padding-top:10px!important
}
.invoice-table-th{
    font-size: 14px;
    color: #868686;
}
.fw-16{
    font-size: 16px;
    font-weight: 400;
    color: #47454B;
}
.assembly-home-nav-item{
    border-radius: 10px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    background: #EEEEEE;
    cursor: pointer;
    font-family: 'Gotham Rounded';
    font-weight: 400;
}
.invoice-location{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: center;
    padding: 5px 16px 5px 8px;
    font-family: 'Gotham Rounded';
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
.visible-hidden:hover .form-check{
    visibility: visible;
}
@media (min-width: 1600px) {
  .total_row {
    padding-left: 1rem !important;
  }
}
</style>
