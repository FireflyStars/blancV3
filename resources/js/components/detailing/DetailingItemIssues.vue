<template>
    <div
        class="stains-title stains-select-text"
        v-if="[0,1,2].includes(issuesStep)"
    >Please select location of the stain</div>
    <div
        class="stains-title stains-select-text"
        v-if="[3,4,5].includes(issuesStep)"
    >Please select location of the damage</div>
    <div class="picto text-align-center" v-if="[0,1,2].includes(issuesStep)">
        <item-picto-new
            :pictoname="typeitemPicto"
            face="all"
            :selectable="true"
            :stainzone="stainZone"
            @add-stain-zone="addStainZone"
            issue_type="stain"
        ></item-picto-new>
    </div>
    <div class="picto text-align-center" v-if="[3,4,5].includes(issuesStep)">
        <item-picto-new
            :pictoname="typeitemPicto"
            face="all"
            :selectable="true"
            :damagezone="damageZone"
            @add-stain-zone="addDamageZone"
            issue_type="damage"
        ></item-picto-new>
    </div>
    <!--
    <div class="picto" v-if="issuesStep == 2">
        <item-picto-new
            :pictoname="typeitemPicto"
            face="all"
            :selectable="false"
            :stainzone="stainZone"
            issue_type="stain" @back-step="back"
        ></item-picto-new>
    </div>
    <div class="picto" v-if="issuesStep == 5">
        <item-picto-new
            :pictoname="typeitemPicto"
            face="all"
            :selectable="false"
            :damagezone="damageZone"
            issue_type="damage" @back-step="back"
        ></item-picto-new>
    </div>
    -->
    <div class="row" v-if="[0,1,2].includes(issuesStep)">
        <div class="stains-title">Any stains ?</div>

        <div>
            To add a stain to the description, please click on its position on the illustration above.
            <br />Then specify the kind of stain.
        </div>

        <!--
        <div class="row">
            <div class="stain-type" v-for="(stain, index) in stainType">{{ stain }}</div>
        </div>
        -->
    </div>

    <div class="row" v-if="[3,4,5].includes(issuesStep)">
        <div class="stains-title">Any damages ?</div>
        <div>
            To add a damage to the description, please click on its position on the illustration above.
            <br />Then specify the kind of damage it is below.
        </div>
        <!--
        <div class="row">
            <div class="stain-type" v-for="(dam, index) in damageKind">{{ dam.name }}</div>
        </div>
        -->
    </div>
    <div class="row mt-4" v-if="[0,1,2].includes(issuesStep)">
        <div class="stains-title">Can you be more specific</div>
        <div>If you know the kind of stain, please indicate below.</div>
        <div class="row">
            <div
                class="stain-type"
                :class="{ selected: stainTags.includes(stain.id) }"
                v-for="(stain, index) in stainKind"
                @click="addTag(stain.id)"
            >{{ stain.name }}</div>
        </div>
        <div class="row free-text">
            <span class="free-text-label">Stains - Additional Comments</span>
            <textarea
                placeholder="Specification about stain"
                class="free-text-input"
                maxlength="140"
                v-model="stain_free_text"
                @keyup.prevent="submit"
                @blur="addFreeText"
            ></textarea>
        </div>
    </div>
    <div class="row mt-4" v-if="[3,4,5].includes(issuesStep)">
        <div class="stains-title">Can you be more specific</div>
        <div>If you know the kind of damage, please indicate below.</div>
        <div class="row">
            <div
                class="stain-type"
                :class="{ selected: damagTags.includes(dam.id) }"
                v-for="(dam, index) in damageKind"
                @click="addTag(dam.id)"
            >{{ dam.name }}</div>
        </div>
        <div class="row free-text">
            <span class="free-text-label">Damages - Additional Comments</span>
            <textarea
                placeholder="Specification about damage"
                class="free-text-input"
                maxlength="140"
                v-model="damage_free_text"
                @keyup.prevent="submit"
                @blur="addFreeText"
            ></textarea><!-- @keyup.enter="addFreeText" -->
        </div>
    </div>
    <div class="row buttons">
        <div class="col-10 text-align-right">
            <button class="btn btn-link btn-previous" @click="back">Previous</button>
        </div>


        <div class="col-2 text-align-right">
            <button class="btn btn-next text-white" @click="save">Next</button>
            <!-- :disabled="!valid" -->
        </div>
    </div>
</template>
<script>
import { ref, watch , nextTick } from 'vue';
import ItemPictoNew from '../miscellaneous/ItemPictoNew.vue'
import {TOASTER_MODULE, TOASTER_MESSAGE} from '../../store/types/types';
import {useStore} from 'vuex';
export default {
    name: "DetailingItemIssues",
    components: { ItemPictoNew },
    props: {
        detailingData: {},
        detailingitem: {},
        typeitemPicto: ''
    },
    emits: ['save-item-issues', 'go-to-step' , 'get-issues-Step'],
    setup(props, context) {
        const store = useStore();
        const stainZone = ref([]);
        const damageZone = ref([]);
        const stainType = ref([]);
        const stainKind = ref([]);
        const valid = ref(false);
        const issuesSteps = ref([]);
        const issuesStep = ref([]);
        const stainTag = ref(0);
        const stainTags = ref([]);
        const damagTags = ref([]);
        const damageTag = ref(0);
        const cur_zone_id = ref(0);
        const timeout =ref('');

        const stain_free_text = ref('');
        const damage_free_text = ref('');
        const damageKind = ref([]);
        stainType.value = ['Oil', 'Organic', 'Light', 'Heavy'];
        stainKind.value = props.detailingData.issues_tags.filter((tag) => tag.type == 'stain');
        damageKind.value = props.detailingData.issues_tags.filter((tag) => tag.type == 'damage');
        issuesSteps.value = [1, 2, 3, 4, 5];
        stainZone.value = props.detailingitem.stains ? JSON.parse(props.detailingitem.stains) : [];
        damageZone.value = props.detailingitem.damages ? JSON.parse(props.detailingitem.damages) : [];
        issuesStep.value = damageZone.value.length > 0 ? 5 : (stainZone.value.length > 0 ? 2 : 0);
        stain_free_text.value = props.detailingitem.stainstext;
        damage_free_text.value = props.detailingitem.damagestext;
        stainTags.value = props.detailingitem.stainsissue != null ? JSON.parse(props.detailingitem.stainsissue) : [];
        damagTags.value = props.detailingitem.damagesissues != null ? JSON.parse(props.detailingitem.damagesissues) : [];

        function setNewIssueValue(val){
            issuesStep.value = val;
        }

        context.emit("get-issues-Step", {
                   issuesStep:issuesStep.value,
            });

        watch(() => issuesStep.value, (current_val,previous_val ) => { 
            context.emit("get-issues-Step", {
                   issuesStep:current_val,
            });
        });

        watch(() => props.detailingitem.stainstext, (current_val,previous_val ) => {
            stain_free_text.value = current_val
            props.detailingitem.stainstext = current_val
        });

        watch(() => props.detailingitem.damagestext, (current_val,previous_val ) => {
            damage_free_text.value = current_val
            props.detailingitem.damagestext = current_val
        });
       
        //stainTag.value = stainZone.value.length > 0 ? stainZone.value[0].id_issue : 0;

        if(stainZone.value.length==1){
            stainTag.value = stainZone.value[0].id_issue;
            //stain_free_text.value = stainZone.value[0].description;

        }else if(stainZone.value.length > 1){
            stainTag.value = stainZone.value[stainZone.value.length - 1].id_issue;
            //stain_free_text.value = stainZone.value[stainZone.value.length - 1].description;
        }


        if(damageZone.value.length==1){
            damageTag.value = damageZone.value[0].id_issue;
        }
        else if(damageZone.value.length > 1){
            damageTag.value = damageZone.value[damageZone.value.length - 1].id_issue;
        }

        valid.value = true;

        /*
        watch(() => stainZone.value, (current_stainZone, previous_stainZone) => {
            valid.value = issuesStep.value == 0 && current_stainZone.length == 0 ? false : true;
        });

        watch(() => damageZone.value, (current_damageZone, previous_damageZone) => {
            valid.value = issuesStep.value == 4 && current_damageZone.length == 0 ? false : true;
        });
        */

        function addStainZone(id) {
            if (!stainZone.value.some(z => z.id_zone === id)) {
                stainZone.value.push({ id_zone: id, id_issue: 0, description: '' });
                //Zone Exist
                issuesStep.value = issuesStep.value == 1 ? 2 : issuesStep.value;
                cur_zone_id.value = id;
                stainTag.value = 0;

            } else {
                stainZone.value.splice(stainZone.value.findIndex((z) => { return z.id_zone === id }), 1);
                cur_zone_id.value = 0;
                stainTag.value = 0;
            }

            //issuesStep.value = issuesStep.value == 0 ? issuesStep.value + 1 : issuesStep.value;
            valid.value = stainZone.value.length > 0;


            context.emit("save-item-issues", {
                detailingitem_id: props.detailingitem.id,
                stains: JSON.stringify(stainZone.value)
            });
        }
        function addDamageZone(id) {
            if (!damageZone.value.some(z => z.id_zone === id)) {
                damageZone.value.push({ id_zone: id, id_issue: 0, description: '' });

                //Zone exist
                issuesStep.value = issuesStep.value == 4 ? 5 : issuesStep.value;
                cur_zone_id.value = id;
                damageTag.value = 0;

            } else {
                damageZone.value.splice(damageZone.value.findIndex((z) => { return z.id_zone === id }), 1);
                cur_zone_id.value = 0;
                damageTag.value = 0;
            }

            //issuesStep.value = issuesStep.value == 3 ? issuesStep.value + 1 : issuesStep.value;
            valid.value = damageZone.value.length > 0;

            context.emit("save-item-issues", {
                detailingitem_id: props.detailingitem.id,
                damages: JSON.stringify(damageZone.value)
            });
        }
        function addTag(tag_id) {
            if (issuesStep.value == 2 || issuesStep.value==0) {

                if (!stainTags.value.includes(tag_id)) {
                    stainTags.value.push(tag_id);
                } else {
                    stainTags.value.splice(stainTags.value.indexOf(tag_id), 1);
                }


                context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    stains: JSON.stringify(stainZone.value),
                    stainisssue : stainTags.value
                });
            }
            if (issuesStep.value == 5 || issuesStep.value==3) {
                //damageTag.value = damageTag.value == tag_id ? 0 : tag_id;

                if (!damagTags.value.includes(tag_id)) {
                    damagTags.value.push(tag_id);
                } else {
                    damagTags.value.splice(damagTags.value.indexOf(tag_id), 1);
                }

                // if(damageZone.value.length==1){
                //     damageZone.value[0].id_issue = damageTag.value;
                // }
                // else if(damageZone.value.length > 1){
                //     let index = damageZone.value.findIndex((z) => { return z.id_zone === cur_zone_id.value });
                //     console.log(damageZone.value , index , cur_zone_id.value)
                //     damageZone.value[index].id_issue = damageTag.value;
                // }

                context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    damages: JSON.stringify(damageZone.value),
                    damagesissues : damagTags.value
                });
            }
        }

        function submit(e) { 

               clearTimeout(timeout.value);
               timeout.value = setTimeout(function(){
                   nextTick(() => {
                     if ([0,1,2].includes(issuesStep.value)) {

                        context.emit("get-free-text", {
                            text_stain : e.target.value,
                            issuesStep :issuesStep.value
                        });
                    }
                    if ([3,4,5].includes(issuesStep.value)) {

                        context.emit("get-free-text", {
                            text_damage: e.target.value,
                            issuesStep :issuesStep.value
                        });
                    }
                });
               }  
              , 500)
            };
        function addFreeText(e) {
            let data = {};
            data['detailingitem_id'] = props.detailingitem.id;
            if ([0,1,2].includes(issuesStep.value)) {
                /*
                //stainZone.value[0].description = e.target.value;
                context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    stains_text: e.target.value,
                    //JSON.stringify(stainZone.value)
                });
                */
                data['stains_text']  = e.target.value;
            }
            if ([3,4,5].includes(issuesStep.value)) {
               /*
               context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    damages_text: e.target.value,
                });
                */
               data['damages_text'] = e.target.value;
            }

            axios.post('/update-detailing-issues-text',data)
                .then((res)=>{
                    if(res.data.updated){
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{
                            message: 'Free text updated',
                            ttl: 5,
                            type: 'success'
                        });
                    }
                }).catch((err)=>{

                }).finally(()=>{

                });

        }
        function save() {
            //Axios to save free text


            if ([3,4,5].includes(issuesStep.value)) {
                context.emit("save-item-issues", {
                    detailingitem_id: props.detailingitem.id,
                    step: 11
                });
            } else if (issuesStep.value == 0) {
                issuesStep.value = 3;
            } else if (issuesStep.value == 2) {
                issuesStep.value = damageZone.value.length > 0 ? 5 : 3;
            } else {
                issuesStep.value++;
            }
            window.scrollTo(0, 0);

        }
        function back() {
            console.log('back called');

            ([3,4,5].includes(issuesStep.value) ? issuesStep.value = 0 :
                [0,1,2].includes(issuesStep.value)? context.emit("go-to-step", 9) :issuesStep.value);
                //(issuesStep.value = issuesStep.value - 1)
            window.scrollTo(0, 0);
        }


        return {
            valid,
            stainZone,
            damageZone,
            stainType,
            stainKind,
            issuesStep,
            stainTag,
            stain_free_text,
            damage_free_text,
            damageKind,
            damageZone,
            damageTag,
            addStainZone,
            addDamageZone,
            addTag,
            addFreeText,
            save,
            back,
            cur_zone_id,
            stainTags,
            damagTags,
            submit,
            setNewIssueValue
        };
    },
}
</script>
<style scoped>
.picto {
    padding: 40px;
}
.stains-title {
    font-family: Gotham Rounded;
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 8px 16px;
    height: 38px;
    background: #ffffff;
    border-radius: 4px;
    font-size: 16px;
    line-height: 140%;
    color: #000000;
}
.stains-select-text {
    font-family: Gotham Rounded;
    font-weight: 800;
    font-size: 18px;
    line-height: 110%;
    justify-content: center;
}
.stain-type {
    font-family: Gotham Rounded;
    width: 110px;
    height: 55px;
    background: #ffffff;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    padding: 10px;
    margin: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 12px;
    color: #c3c3c3;
    /* white-space: nowrap;
    text-overflow: ellipsis; */
}
.stain-type:hover {
    background-color: #d3e7cc;
}
.stain-type.selected {
    background-color: #47454b;
    color: #ffffff;
}
.buttons {
    padding: 10px 60px;
}
.btn-next {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 18px 14px;
    width: 148px;
    height: 41px;
    border-radius: 4px;
    background: #42a71e;
    outline: none !important;
    box-shadow: none !important;
}
.btn-next:disabled {
    background: #e0e0e0;
}
.btn-previous {
    font-size: 16px;
    line-height: 19px;
    align-items: center;
    text-align: center;
    text-decoration-line: underline;
}
.free-text-label {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 140%;
    display: flex;
    align-items: flex-end;
    color: #000000;
}
.free-text-input {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 16px;
    border: 1px solid #868686;
    box-sizing: border-box;
    border-radius: 5px;
    flex: none;
    order: 2;
    align-self: stretch;
    flex-grow: 1;
    margin: 8px 0px;
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 140%;
    color: #151920;
}
.free-text-input::placeholder {
    color: #868686;
}
</style>
