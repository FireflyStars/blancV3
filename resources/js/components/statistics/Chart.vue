<template>
    <ProductionFilter :filterVal="filterVal" @update:filterVal="newValue => filterVal = newValue"></ProductionFilter>
    <div class="p-3 bg-black">
        <div class="d-flex">
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <h3 class="font-20 gotham-rounded-medium d-flex">
                        <div>Sales by</div>
                        <div class="px-2">
                            <select class="form-select form-select-sm d-flex" v-model="pieChart1">
                                <option>channel</option>
                                <option>item type</option>
                                <option>department</option>
                                <option>service</option>
                                <option>payment</option>
                            </select>
                        </div>
                    </h3>
                    <div class="d-flex">
                        <div class="col-7">
                            <div class="d-flex" v-for="(channel, index) in salesByChannelChartData" :key="index">
                                <div class="col-1 d-flex align-items-center">
                                    <div class="origin-dot rounded-circle" v-if="index==0" :style="{'background-color': '#EB5757'}"></div>
                                    <div class="origin-dot rounded-circle" v-if="index==1" :style="{'background-color': '#5200FF'}"></div>
                                    <div class="origin-dot rounded-circle" v-if="index==2" :style="{'background-color': '#8ADFDF'}"></div>
                                    <div class="origin-dot rounded-circle" v-if="index==3" :style="{'background-color': '#80A2EC'}"></div>
                                    <div class="origin-dot rounded-circle" v-if="index==4" :style="{'background-color': '#EEE516'}"></div>
                                    <div class="origin-dot rounded-circle" v-if="index==5" :style="{'background-color': '#1F78B4'}"></div>
                                </div>
                                <div class="col-6">{{ channel.channel }}</div>
                                <div class="col-5">£{{ channel.amount }}</div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div id="saleByOrigin" style="height: 150px">
                                
                            </div>
                            <div class="mt-3" v-if="pieChart1== 'item type' || pieChart1== 'department' ||  pieChart1== 'service'">
                                <span class="fw-bold text-danger">Other :</span> 
                                <span v-if="salesByTypeitemTotal > salesByTypeitemTotalOfItem">£{{ salesByTypeitemTotal - salesByTypeitemTotalOfItem }}</span>
                                <span class="fw-bold text-danger" v-else> - £{{ salesByTypeitemTotalOfItem - salesByTypeitemTotal }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <h3 class="font-20 gotham-rounded-medium d-flex">
                        <div class="">Pieces by</div>
                        <div class="px-2">
                            <select class="form-select form-select-sm d-flex" v-model="pieChart2">
                                <option>channel</option>
                                <option>item type</option>
                                <option>department</option>
                            </select>
                        </div>
                    </h3>
                    <div class="d-flex">
                        <div class="col-7">
                            <div class="d-flex" v-for="(item, index) in piecesByItemChartData" :key="index">
                                <div class="col-1 d-flex align-items-center">
                                    <div class="origin-dot rounded-circle" v-if="index==0" :style="{'background-color': '#EB5757'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==1" :style="{'background-color': '#5200FF'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==2" :style="{'background-color': '#8ADFDF'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==3" :style="{'background-color': '#80A2EC'}"></div>
                                    <div class="origin-dot rounded-circle" v-else-if="index==4" :style="{'background-color': '#EEE516'}"></div>
                                    <div class="origin-dot rounded-circle" v-else :style="{'background-color': '#1F78B4'}"></div>
                                </div>
                                <div class="col-6">{{ item.name }}</div>
                                <div class="col-5">{{ item.amount }}</div>
                            </div>
                        </div>
                        <div class="col-5" id="saleByClient" style="height: 150px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 p-2 d-flex">
                <div class="rounded-3 bg-white w-100 p-2">
                    <div class="d-flex">
                        <h3 class="font-20 gotham-rounded-medium m-0">Average Order Value</h3>
                        <TotalPercent class="ms-5" :amount="avgOrder" :pastAmount="avgOrderToCompare"></TotalPercent>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="col-5">
                            <h4 class="font-16 gotham-rounded-medium">by customer type</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12 gotham-rounded-book">B2B</p>
                                    <p class="w-100 text-center font-16 gotham-rounded-medium align-self-end">£{{ b2bAVGSale }}</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12 gotham-rounded-book">B2C</p>
                                    <p class="w-100 text-center font-16 gotham-rounded-medium align-self-end">£{{ b2cAVGSale }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h4 class="font-16 gotham-rounded-medium">by channel</h4>
                            <div class="d-flex flex-wrap">
                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12 gotham-rounded-book">Corp Del</p>
                                    <p class="w-100 text-center font-16 gotham-rounded-medium align-self-end">£{{ corpDel }}</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 me-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12 gotham-rounded-book">Home Del</p>
                                    <p class="w-100 text-center font-16 gotham-rounded-medium align-self-end">£{{ homeDel }}</p>
                                </div>
                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                    <p class="w-100 text-center font-12 gotham-rounded-book">Stores</p>
                                    <p class="w-100 text-center font-16 gotham-rounded-medium align-self-end">£{{ storeDel }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-3 bg-white">
        <div class="d-flex">
            <div class="col-7">
                <div class="bg-gray p-3 rounded-3">
                    <div class="d-flex align-items-center p-2">
                        <h3 class="font-20 gotham-rounded-medium m-0">Total sales over time</h3>
                        <TotalPercent class="ms-5" :amount="salesByChannelTotal" :pastAmount="salesByChannelTotalToCompare"></TotalPercent>
                        <div class="d-flex ms-auto">
                            <h4 @click="downloadExcel" class="mb-0 me-4 font-14 text-custom-success gotham-rounded-medium text-decoration-underline cursor-pointer"><em>View Report</em></h4>
                            <h4 @click="downloadOrderCSV" class="mb-0 font-14 text-custom-success gotham-rounded-medium text-decoration-underline cursor-pointer"><em>View Order</em></h4>
                        </div>
                    </div>
                    <div class="legends bg-white p-2">
                        <div class="d-flex">
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="allSaleByDateLegend" :checked_checkbox="true">All sales</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="corpDelByDateLegend" :checked_checkbox="false">Corp Del</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="homeDelByDateLegend" :checked_checkbox="false">Home Del</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="MBByDateLegend" :checked_checkbox="false">MB</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="NHByDateLegend" :checked_checkbox="false">NH</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="CHByDateLegend" :checked_checkbox="false">CH</CheckBox></div>
                            <div class="d-flex justify-content-center mx-2"><CheckBox v-model="SKByDateLegend" :checked_checkbox="false">SK</CheckBox></div>
                        </div>
                    </div>
                    <div class="total-chart bg-white" id="totalChart">

                    </div>
                </div>
                <div class="bg-gray p-3 rounded-3 mt-3">
                    <div class="d-flex">
                        <div class="col-6">
                            <h3 class="font-20 gotham-rounded-medium">Ratios</h3>
                            <h4 class="font-16 gotham-rounded-medium mt-3">Sales</h4>
                            <div class="d-flex align-items-center" v-for="(saleByUser, index) in salesByUser" :key="index">
                                <div class="col-3">{{ saleByUser.name }}</div>
                                <TotalPercent class="col-4" :fontSize="14" :amount="saleByUser.amount" :pastAmount="saleByUser.pastAmount"></TotalPercent>
                                <TotalPercent class="ms-3" :fontSize="14" :amount="saleByUser.hour" :symbol="'h'" :pastAmount="saleByUser.pastHour"></TotalPercent>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <h3 class="font-20 gotham-rounded-medium m-0">&nbsp;</h3>
                                <h4 class="mb-0 ms-auto font-14 text-custom-success text-decoration-underline gotham-rounded-medium cursor-pointer"><em>View report</em></h4>
                            </div>
                            <h4 class="font-16 gotham-rounded-medium mt-3">Operations</h4>
                            <div class="d-flex" v-for="(saleByCommande, index) in salesByCommande" :key="index">
                                <div class="col-3">{{ saleByCommande.id }}</div>
                                <div class="col-3">{{ saleByCommande.name }}</div>
                                <div class="col-3">{{ saleByCommande.amount }}£</div>
                                <div class="col-3">{{ saleByCommande.hour }}h</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5 ps-3">
                <div class="bg-gray rounded-3 p-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 gotham-rounded-medium m-0">Sign ups</h3>
                        <TotalPercent class="ms-5" :amount="totalSignUpCount" :symbol="''" :pastAmount="totalSignUpCountPast"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success gotham-rounded-medium text-decoration-underline cursor-pointer"><em>View report</em></h4>
                    </div>
                    <div class="d-flex">
                        <div class="col-6">
                            <h4 class="font-16 gotham-rounded-medium mt-3">by channel</h4>
                            <div class="d-flex" v-for="(signup, index) in signupByChannel" :key="index">
                                <div class="col-6">{{ signup.TypeDelivery }}</div>
                                <div class="col-6 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="signup.count" :symbol="''" :pastAmount="signup.pastCount"></TotalPercent>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="font-16 gotham-rounded-medium mt-3">by customer type</h4>
                            <div class="d-flex">
                                <div class="col-6">B2B</div>
                                <div class="col-6 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="signupB2B" :symbol="''" :pastAmount="signupB2BPast"></TotalPercent>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="col-6">B2C</div>
                                <div class="col-6 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="signupB2C" :symbol="''" :pastAmount="signupB2CPast"></TotalPercent>
                                </div>
                            </div>
                            <h4 class="font-16 gotham-rounded-medium mt-3">by sign up origin</h4>
                            <div class="d-flex">
                                <div class="col-6">APP</div>
                                <div class="col-6 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="signupAPP" :symbol="''" :pastAmount="signupAPPPast"></TotalPercent>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="col-6">POS</div>
                                <div class="col-6 d-flex align-items-center">
                                    <TotalPercent :fontSize="14" :amount="signupPOS" :symbol="''" :pastAmount="signupPOSPast"></TotalPercent>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray rounded-3 p-3 mt-3">
                    <div class="d-flex align-items-center">
                        <h3 class="font-20 gotham-rounded-medium m-0">Bookings</h3>
                        <TotalPercent class="ms-5" :amount="totalBookingCount" :symbol="''" :pastAmount="totalBookingCountPast"></TotalPercent>
                        <h4 class="mb-0 ms-auto font-14 text-custom-success gotham-rounded-medium text-decoration-underline cursor-pointer"><em>View report</em></h4>
                    </div>
                    <div class="d-flex mt-3">
                        <!-- <div class="col-6">
                            <h4 class="font-16 gotham-rounded-medium mt-3">by channel</h4>
                            <div class="d-flex align-items-center" v-for="(booking, index) in bookingByChannel" :key="index">
                                <div class="col-4">{{ booking.TypeDelivery }}</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="booking.count" :symbol="''" :pastAmount="booking.pastCount"></TotalPercent>
                            </div>
                        </div> -->
                        <div class="col-6">
                            <h4 class="font-16 gotham-rounded-medium mt-3">by customer type</h4>
                            <div class="d-flex align-items-center">
                                <div class="col-4">B2B</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="bookingB2B" :symbol="''" :pastAmount="bookingB2BPast"></TotalPercent>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-4">B2C</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="bookingB2C" :symbol="''" :pastAmount="bookingB2CPast"></TotalPercent>
                            </div>
                            <h4 class="font-16 gotham-rounded-medium mt-3">by booking origin</h4>
                            <div class="d-flex align-items-center">
                                <div class="col-4">APP</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="bookingAPP" :symbol="''" :pastAmount="bookingAPPPast"></TotalPercent>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-4">POS</div>
                                <TotalPercent :fontSize="14" class="ms-5" :amount="bookingPOS" :symbol="''" :pastAmount="bookingPOSPast"></TotalPercent>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'
import { ref, watch, onMounted, onUnmounted } from 'vue'
import TotalPercent from './TotalPercent';
import ProductionFilter from "./ProductionFilter";
import {
    LOADER_MODULE,
    DISPLAY_LOADER,
    HIDE_LOADER,
}
from '../../store/types/types'
import CheckBox from '../miscellaneous/CheckBox';
import * as am5 from "@amcharts/amcharts5";
import * as am5percent from "@amcharts/amcharts5/percent";
import * as am5xy from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import axios from 'axios';
import exportFromJSON from "export-from-json";

export default {
    components:{
        CheckBox,
        TotalPercent,
        ProductionFilter,
    },
    setup(){
        const store = useStore();
        const allSaleByDateLegend = ref(true);
        const corpDelByDateLegend = ref(false);
        const homeDelByDateLegend = ref(false);
        const MBByDateLegend = ref(false);
        const NHByDateLegend = ref(false);
        const CHByDateLegend = ref(false);
        const SKByDateLegend = ref(false);
        const pieChart1 = ref('channel');
        const pieChart2 = ref('item type');
        const salesByChannelTotal = ref(0);
        const salesByTypeitemTotal = ref(0);
        const salesByTypeitemTotalOfItem = ref(0);
        const salesByChannelTotalToCompare = ref(0);
        const salesByItemTotal = ref(0);
        const salesByItemTotalToCompare = ref(0);

        const b2bAVGSale = ref(0);
        const b2cAVGSale = ref(0);
        const salesByCustType = ref(0);
        const avgOrder = ref(0);
        const avgOrderToCompare = ref(0);
        const homeDel = ref(0);
        const corpDel = ref(0);
        const storeDel = ref(0);
        const salesByCommande = ref([]);
        const salesByUser = ref([]);

        const bookingByChannel = ref([]);
        const bookingB2B = ref(0);
        const bookingB2BPast = ref(0);
        const bookingB2C = ref(0);
        const bookingB2CPast = ref(0);
        const bookingAPP = ref(0);
        const bookingAPPPast = ref(0);
        const bookingPOS = ref(0);
        const bookingPOSPast = ref(0);
        const totalBookingCount = ref(0);
        const totalBookingCountPast = ref(0);

        const signupByChannel = ref([]);
        const signupB2B = ref(0);
        const signupB2BPast = ref(0);
        const signupB2C = ref(0);
        const signupB2CPast = ref(0);
        const signupAPP = ref(0);
        const signupAPPPast = ref(0);
        const signupPOS = ref(0);
        const signupPOSPast = ref(0);
        const totalSignUpCount = ref(0);
        const totalSignUpCountPast = ref(0);

        const today = new Date();
        const filterVal = ref({
            customFilter: 0,
            startDate: `${today.getFullYear()}-${(today.getMonth()+1).toString().padStart(2, '0')}-${today.getDate()}`,
            endDate:`${today.getFullYear()}-${(today.getMonth()+1).toString().padStart(2, '0')}-${today.getDate()}`,
            dateRangeType:'Today',
            compareMode: 'month',
            compareCustomFilter: false,
            compareStartDate: `${today.getFullYear()-1}-${today.getMonth().toString().padStart(2, 0)}-${today.getDate()}`,
            compareEndDate: `${today.getFullYear()-1}-${today.getMonth().toString().padStart(2, 0)}-${today.getDate()}`,
        });
        onMounted(()=>{
            getProdStats();
        })
        onUnmounted(()=>{
            destroyChart();
        })
        const destroyChart = ()=>{
            if(salesByChannelChartRoot)
                salesByChannelChartRoot.dispose();
            if(piecesByItemChartRoot)
                piecesByItemChartRoot.dispose();
            if(totalRoot)
                totalRoot.dispose();
        }
        watch(() => filterVal.value, () => {
            getProdStats();
        });
        const getProdStats = ()=>{
            if(salesByChannelChartRoot)
                destroyChart();
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading data...']);
            axios.post('/get-prod-statistics', 
                {...filterVal.value, ... { salesType: pieChart1.value, pieceType: pieChart2.value }}
            ).then((res) => {
                salesByChannelChartData.value = res.data.salesByChannel;
                salesByChannelTotal.value = res.data.salesByChannelTotal;
                salesByChannelTotalToCompare.value = res.data.salesByChannelTotalToCompare;
                salesByTypeitemTotal.value = res.data.salesByTypeitemTotal;
                salesByTypeitemTotalOfItem.value = res.data.salesByTypeitemTotalOfItem;

                piecesByItemChartData.value = res.data.piecesByItem;
                salesByItemTotal.value = res.data.salesByItemTotal;
                salesByItemTotalToCompare.value = res.data.salesByItemTotalToCompare;

                avgOrder.value = res.data.avgOrder;
                avgOrderToCompare.value = res.data.avgOrderToCompare;
                b2bAVGSale.value = res.data.b2bAVGSale;
                b2cAVGSale.value = res.data.b2cAVGSale;
                homeDel.value = res.data.homeDel;
                corpDel.value = res.data.corpDel;
                storeDel.value = res.data.storeDel;

                allSaleData.value = res.data.allSaleData;
                corpDelSaleData.value = res.data.corpDelSaleData;
                homeDelSaleData.value = res.data.homeDelSaleData;
                MBSaleData.value = res.data.MBSaleData;
                NHSaleData.value = res.data.NHSaleData;
                CHSaleData.value = res.data.CHSaleData;
                SKSaleData.value = res.data.SKSaleData;

                salesByCommande.value = res.data.salesByCommande;
                salesByUser.value = res.data.salesByUser;

                bookingByChannel.value = res.data.bookingByChannel;
                bookingB2B.value = res.data.bookingB2B;
                bookingB2BPast.value = res.data.bookingB2BPast;
                bookingB2C.value = res.data.bookingB2C;
                bookingB2CPast.value = res.data.bookingB2CPast;
                bookingAPP.value = res.data.bookingAPP;
                bookingAPPPast.value = res.data.bookingAPPPast;
                bookingPOS.value = res.data.bookingPOS;
                bookingPOSPast.value = res.data.bookingPOSPast;
                totalBookingCount.value = res.data.totalBookingCount;
                totalBookingCountPast.value = res.data.totalBookingCountPast;

                signupByChannel.value = res.data.signupByChannel;
                signupB2B.value = res.data.signupB2B;
                signupB2BPast.value = res.data.signupB2BPast;
                signupB2C.value = res.data.signupB2C;
                signupB2CPast.value = res.data.signupB2CPast;
                signupAPP.value = res.data.signupAPP;
                signupAPPPast.value = res.data.signupAPPPast
                signupPOS.value = res.data.signupPOS;
                signupPOSPast.value = res.data.signupPOSPast
                totalSignUpCount.value = res.data.totalSignUpCount;
                totalSignUpCountPast.value = res.data.totalSignUpCountPast;

                initSalesByChannelChart();
                initPiecesByItemChart();
                initTotalChart();
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            });
        }
        watch(() => pieChart1.value, () => {
            getProdStats();
        });
        watch(() => pieChart2.value, () => {
            getProdStats();
        });
        let salesByChannelChartRoot = null;
        let salesByChannelChart = null;
        let salesByChannelChartSeries= null;
        const salesByChannelChartData = ref([
        ]);
        const initSalesByChannelChart = ()=>{
            // Define data for sales by origin
            // Create root element
            // if(salesByChannelChartRoot == null){
            //     }
            salesByChannelChartRoot = am5.Root.new("saleByOrigin");
            // Set themes
            salesByChannelChartRoot.setThemes([
                am5themes_Animated.new(salesByChannelChartRoot)
            ]);
            // Create chart
            salesByChannelChart = salesByChannelChartRoot.container.children.push(am5percent.PieChart.new(salesByChannelChartRoot, {
                radius: am5.percent(70),
                innerRadius: 70,
                layout: salesByChannelChartRoot.verticalLayout
            }));
            // hide logo
            salesByChannelChart._root._logo._childrenDisplay.visible  = false;
            // salesByChannelChart._root._logo._display.visible  = false;
            // salesByChannelChart._root._logo._display.scale  = 0;
            // Create series
            salesByChannelChartSeries = null;
            salesByChannelChartSeries = salesByChannelChart.series.push(am5percent.PieSeries.new(salesByChannelChartRoot, {
                name: "Series",
                valueField: "amount",
                categoryField: "channel",
            }));
            salesByChannelChartSeries.get("colors").set("colors", [
                am5.color(0xEB5757),
                am5.color(0x5200FF),
            ]);
            salesByChannelChartSeries.labels.template.set("forceHidden", true);
            salesByChannelChartSeries.ticks.template.set("visible", false);
            // Add label
            let percent = '';
            if(salesByChannelTotal.value == salesByChannelTotalToCompare.value){
                percent = `[#0f0][fontStyle: italic]0%[/][/]`;
            }
            if(salesByChannelTotal.value > salesByChannelTotalToCompare.value && salesByChannelTotalToCompare.value != 0){
                percent = `[#0f0][fontStyle: italic]${((salesByChannelTotal.value / salesByChannelTotalToCompare.value -1)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByChannelTotal.value < salesByChannelTotalToCompare.value && salesByChannelTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${((1 - salesByChannelTotal.value / salesByChannelTotalToCompare.value)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByChannelTotal.value > salesByChannelTotalToCompare.value && salesByChannelTotalToCompare.value == 0){
                percent = "--";
            }
            // let originLabel;
            let originLabel = salesByChannelChartSeries.children.push(am5.Label.new(salesByChannelChartRoot, {
                text: "£"+ salesByChannelTotal.value + "\n" + percent,
                textAlign: "center",
                centerX: am5.percent(50),
                centerY: am5.percent(50),
                fontSize: 20,
                populateText: true
            }));
            // if(pieChart1.value == 'channel'){
            // }else{
            //     originLabel = salesByChannelChartSeries.children.push(am5.Label.new(salesByChannelChartRoot, {
            //         text: "£{valueSum.formatNumber('#,###.')}\n   "+ percent,
            //         centerX: am5.percent(50),
            //         centerY: am5.percent(50),
            //         fontSize: 20,
            //         populateText: true
            //     }));
            // }
            // Set data
            salesByChannelChartSeries.data.setAll(salesByChannelChartData.value);
            salesByChannelChartSeries.onPrivate("valueSum", function(){
                originLabel.text.markDirtyText();
            })
            salesByChannelChartSeries.appear(1000, 100);
        }

        let piecesByItemChartRoot = null;
        let piecesByItemChart = null;
        let piecesByItemChartSeries = null;
        // Define data for sales by customer
        const piecesByItemChartData = ref([
        ]);
        const initPiecesByItemChart = ()=>{
            // Create root element
            if(piecesByItemChartRoot ==  null){
                }
            piecesByItemChartRoot = am5.Root.new("saleByClient");
            // Set themes
            piecesByItemChartRoot.setThemes([
                am5themes_Animated.new(piecesByItemChartRoot)
            ]);
            // Create chart
            piecesByItemChart = piecesByItemChartRoot.container.children.push(am5percent.PieChart.new(piecesByItemChartRoot, {
                radius: am5.percent(70),
                innerRadius: 70,
                layout: piecesByItemChartRoot.verticalLayout
            }));
            // hide logo
            piecesByItemChart._root._logo._childrenDisplay.visible  = false;
            // piecesByItemChart._root._logo._display.visible  = false;
            // piecesByItemChart._root._logo._display.scale  = 0;
            // Create series
            piecesByItemChartSeries = piecesByItemChart.series.push(am5percent.PieSeries.new(piecesByItemChartRoot, {
                name: "Series",
                valueField: "amount",
                categoryField: "name",
            }));
            piecesByItemChartSeries.get("colors").set("colors", [
                am5.color(0xEB5757),
                am5.color(0x5200FF),
                am5.color(0x8ADFDF),
                am5.color(0x80A2EC),
                am5.color(0xEEE516),
                am5.color(0x1F78B4)
            ]);
            piecesByItemChartSeries.labels.template.set("forceHidden", true);
            piecesByItemChartSeries.ticks.template.set("visible", false);
            // Add label
            let percent = '';
            if(salesByItemTotal.value == salesByItemTotalToCompare.value){
                percent = `[#0f0][fontStyle: italic]0%[/][/]`;
            }
            if(salesByItemTotal.value > salesByItemTotalToCompare.value && salesByItemTotalToCompare.value != 0){
                percent = `[#0f0][fontStyle: italic]${((salesByItemTotal.value / salesByItemTotalToCompare.value-1)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByItemTotal.value < salesByItemTotalToCompare.value && salesByItemTotalToCompare.value != 0){
                percent = `[#f00][fontStyle: italic]${((1 - salesByItemTotal.value / salesByItemTotalToCompare.value)* 100).toFixed(0)}%[/][/]`;
            }
            if(salesByItemTotal.value > salesByItemTotalToCompare.value && salesByItemTotalToCompare.value == 0){
                percent = "--";
            }
            // let clientLabel;
            let clientLabel = piecesByItemChartSeries.children.push(am5.Label.new(piecesByItemChartRoot, {
                text: salesByItemTotal.value + "\n" + percent,
                textAlign: "center",
                centerX: am5.percent(50),
                centerY: am5.percent(50),
                fontSize: 20,
                populateText: true
            }));
            // if(pieChart2.value == 'item type'){
            // }else{
            //     clientLabel = piecesByItemChartSeries.children.push(am5.Label.new(piecesByItemChartRoot, {
            //         text: "{valueSum.formatNumber('#,###.')}\n   " + percent,
            //         centerX: am5.percent(50),
            //         centerY: am5.percent(50),
            //         fontSize: 20,
            //         populateText: true
            //     }));
            // }
            // Set data
            piecesByItemChartSeries.data.setAll(piecesByItemChartData.value);
            piecesByItemChartSeries.onPrivate("valueSum", function(){
                clientLabel.text.markDirtyText();
            })
            piecesByItemChartSeries.appear(1000, 100);
        }
        let totalRoot = null;
        let totalChart = null;
        let xAxis = null;
        let yAxis = null;
        let allSale = null;
        const allSaleData = ref([]);
        let corpDelSale = null;
        const corpDelSaleData = ref([]);
        let homeDelSale = null;
        const homeDelSaleData = ref([]);
        let MBSale = null;
        const MBSaleData = ref([]);
        let NHSale = null;
        const NHSaleData = ref([]);
        let CHSale = null;
        const CHSaleData = ref([]);
        let SKSale = null;
        const SKSaleData = ref([]);
        const initTotalChart = ()=>{
            // if(totalRoot == null){
            // }
            totalRoot = am5.Root.new("totalChart");
            totalRoot.setThemes([
                am5themes_Animated.new(totalRoot)
            ]);
            // Create chart
            totalChart = totalRoot.container.children.push(am5xy.XYChart.new(totalRoot, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX: true
            }));
            // hide logo
            totalChart._root._logo._childrenDisplay.visible  = false;
            // totalChart._root._logo._display.visible  = false;
            // totalChart._root._logo._display.scale  = 0;
            // Add cursor
            let cursor = totalChart.set("cursor", am5xy.XYCursor.new(totalRoot, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);


            // Create axes
            xAxis = totalChart.xAxes.push(am5xy.DateAxis.new(totalRoot, {
                maxDeviation: 0.2,
                baseInterval: {
                    timeUnit: "day",
                    count: 1
                },
                renderer: am5xy.AxisRendererX.new(totalRoot, {}),
                tooltip: am5.Tooltip.new(totalRoot, {})
            }));
            yAxis = totalChart.yAxes.push(am5xy.ValueAxis.new(totalRoot, {
                renderer: am5xy.AxisRendererY.new(totalRoot, {})
            }));
            // Add scrollbar
            totalChart.set("scrollbarX", am5.Scrollbar.new(totalRoot, {
                orientation: "horizontal"
            }));

            addSeries(1);
            totalChart.appear(1000, 100);
        }

        const addSeries = (seriesIndex)=>{
            // Add series
            if(seriesIndex == 1){
                allSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                allSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                allSale.data.setAll(allSaleData.value);
                // Make stuff animate on load
                allSale.appear(1000);
            }else if(seriesIndex == 2){
                corpDelSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                corpDelSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                corpDelSale.data.setAll(corpDelSaleData.value);
                // Make stuff animate on load
                corpDelSale.appear(1000);
            }else if(seriesIndex == 3){
                homeDelSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                homeDelSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                homeDelSale.data.setAll(homeDelSaleData.value);
                // Make stuff animate on load
                homeDelSale.appear(1000);
            }else if(seriesIndex == 4){
                MBSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                // Generate random data
                MBSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                MBSale.data.setAll(MBSaleData.value);
                // Make stuff animate on load
                MBSale.appear(1000);
            }else if(seriesIndex == 5){
                NHSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                NHSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                NHSale.data.setAll(NHSaleData.value);
                // Make stuff animate on load
                NHSale.appear(1000);
            }else if(seriesIndex == 6){
                CHSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                CHSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                CHSale.data.setAll(CHSaleData.value);
                // Make stuff animate on load
                CHSale.appear(1000);
            }else{
                SKSale = totalChart.series.push(am5xy.LineSeries.new(totalRoot, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "amount",
                    valueXField: "date",
                    tooltip: am5.Tooltip.new(totalRoot, {
                        labelText: "{valueY}"
                    })
                }));
                SKSale.data.processor = am5.DataProcessor.new(totalRoot, {
                    dateFields: ["date"], dateFormat: "yyyy-MM-dd"
                });
                SKSale.data.setAll(SKSaleData.value);
                // Make stuff animate on load
                SKSale.appear(1000);
            }
        }
        const removeSeries = (series)=>{
            totalChart.series.removeIndex(
                totalChart.series.indexOf(series)
            );
        }
        watch( ()=> allSaleByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(1);
            }else{
                removeSeries(allSale);
            }
        });
        watch( ()=> corpDelByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(2);
            }else{
                removeSeries(corpDelSale);
            }
        });
        watch( ()=> homeDelByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(3);
            }else{
                removeSeries(homeDelSale);
            }
        });
        watch( ()=> MBByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(4);
            }else{
                removeSeries(MBSale);
            }
        });
        watch( ()=> NHByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(5);
            }else{
                removeSeries(NHSale);
            }
        });
        watch( ()=> CHByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(6);
            }else{
                removeSeries(CHSale);
            }
        });
        watch( ()=> SKByDateLegend.value, (cur_val, pre_val)=>{
            if(cur_val){
                addSeries(7);
            }else{
                removeSeries(SKSale);
            }
        });

        const downloadOrderCSV = ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'downloading order...']);
            axios.post('/get-order-csv', filterVal.value).then((res) => {
                const data = res.data.data;
                const fileName = res.data.fileName;
                const exportType = exportFromJSON.types.csv;

                if (data) exportFromJSON({ data, fileName, exportType });
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        const downloadExcel = ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'downloading report...']);
            axios({
                    url: 'get-excel-report', // File URL Goes Here
                    method: 'post',
                    data: filterVal.value,
                    responseType: 'blob',
                }).then((res) => {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    const url = window.URL.createObjectURL(new Blob([res.data]))
                     
                    const link = document.createElement('a')
                    link.href = url
                    var filename = res.headers['content-disposition'].split(';')[1].split('=')[1].replace('"', '').replace('"', '');                    
                    link.setAttribute('download', filename)
                    document.body.appendChild(link)
                    link.click()
                });
        }
        return {
            allSaleByDateLegend,
            corpDelByDateLegend,
            homeDelByDateLegend,
            MBByDateLegend,
            NHByDateLegend,
            CHByDateLegend,
            SKByDateLegend,
            filterVal,
            pieChart1,
            pieChart2,
            salesByChannelChartData,
            piecesByItemChartData,

            allSaleData,
            corpDelSaleData,
            homeDelSaleData,
            MBSaleData,
            NHSaleData,
            CHSaleData,
            SKSaleData,

            salesByChannelTotal,
            salesByChannelTotalToCompare,
            salesByTypeitemTotal,
            salesByTypeitemTotalOfItem,
            salesByItemTotal,
            salesByItemTotalToCompare,

            b2bAVGSale,
            b2cAVGSale,
            salesByCustType,
            avgOrder,
            avgOrderToCompare,
            homeDel,
            corpDel,
            storeDel,
            salesByCommande,
            salesByUser,

            bookingByChannel,
            bookingB2B,
            bookingB2BPast,
            bookingB2C,
            bookingB2CPast,
            bookingAPP,
            bookingAPPPast,
            bookingPOS,
            bookingPOSPast,
            totalBookingCount,
            totalBookingCountPast,

            signupByChannel,
            signupB2B,
            signupB2BPast,
            signupB2C,
            signupB2CPast,
            signupAPP,
            signupAPPPast,
            signupPOS,
            signupPOSPast,
            totalSignUpCount,
            totalSignUpCountPast,

            downloadOrderCSV,
            downloadExcel,
        }
    }
}
</script>

<style lang="scss" scoped>
.origin-dot{
    width: 14px;
    height: 14px;
}
.avg-sale-block{
    min-width: 75px;
    max-width: 75px;
    height: 95px;
    background: #E0E0E0;
    border-radius: 10px;
}
.total-chart{
    min-height: 400px;
}
.text-custom-success{
    color: #42A71E;
}
.bg-gray{
    background: #F8F8F8;
}
</style>