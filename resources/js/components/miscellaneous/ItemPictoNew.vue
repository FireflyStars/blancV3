<template>
    <svg
        version="1.1"
        id="Layer_1"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        x="0px"
        y="0px"
        :enable-background="'new ' + svg_viewpoint"
        xml:space="preserve"
        style="pointer-events: fill"
        @click="onPartClick"
    >
        <g id="svg_path" v-html="picto_details" />
    </svg>

</template>
<script>
import { ref, watch} from "vue";
import { useStore } from "vuex";
 import {useRoute} from 'vue-router';
import {
    LOADER_MODULE,
    SET_LOADER_MSG,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from "../../store/types/types";

export default {
    name: "ItemPictoNew",
    props: ["pictoname", "face", "selectable", "issue_type", "stainzone", "damagezone"],
    emits: ['add-stain-zone','get-zone-detail','back-step'],
    setup(props, context) {
        const svg_viewpoint = ref("");
        const svg_scale = ref("");
        const picto_details = ref("");
        const svg_data = ref({});
        const svg_viewbox = ref([]);

        const store = useStore();
        const route = useRoute();


        const loadSvgZones = (type_picto) => {

            let zones = [
                { position: "sleave", face: "front", side: "left" },
                { position: "bottom", face: "back", side: "" },
                { position: "collar", face: "front", side: "" },
            ];
            let details = "";

            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [
                true,
                "Please wait....",
            ]);
            axios
                .post("/item-picto", {
                    item_type: type_picto,
                    zones: JSON.stringify(zones),
                    all_zones: 1,
                    face: "",//props.face ? props.face : "front", //all or front
                })
                .then((res) => {
                    console.log(res.data);

                    if (res.data.svg_details) {
                        svg_viewpoint.value = res.data.svg_details.viewpoint;
                        svg_scale.value = res.data.svg_details.scale;

                        document.getElementById('Layer_1').setAttribute('viewBox', String(res.data.svg_details.viewpoint));

                    }

                    if (res.data.svg.length > 0) {
                        let svg = res.data.svg;
                        svg_data.value = res.data.svg;
                        svg.forEach((v) => {
                            let svg_el = "";
                            let fill_class = "";
                            let index=0;
                            if (props.issue_type == "stain" && v.description && props.stainzone && props.stainzone.some(z => z.id_zone === v.id)) {

                                fill_class = props.selectable ? 'stain-editable' : 'stain-not-editable';
                                index = props.stainzone.findIndex(z => z.id_zone === v.id)+1;
                            } else if (props.issue_type == "damage" && v.description && props.damagezone && props.damagezone.some(z => z.id_zone === v.id)) {

                                fill_class = props.selectable ? 'damage-editable' : 'damage-not-editable';
                                index = props.damagezone.findIndex(z => z.id_zone === v.id)+1;
                           }
                            if (v.svg_type == "path") {
                                svg_el =
                                    '<path fill="none" id="path_' +
                                    v.id +
                                    '" class="each-svg-el each-path-' +
                                    v.face.toUpperCase() +
                                    " path_" +
                                    v.description +
                                    ' ' + (v.description != '' ? 'clickable-path' : '') +
                                    ' ' + fill_class + '"' +
                                    (v.stroke == 1 ? 'stroke="#333333"' : "") +
                                    ' stroke-width="2" d="' +
                                    v.svg_path +
                                    '" ' +
                                    (v.clickable == 1
                                        ? 'class="is_zone" style="cursor:pointer;"'
                                        : "") +
                                    ' data-pos="' +
                                    (v.clickable == 1
                                        ? v.position +
                                        " - " +
                                        v.face +
                                        " - " +
                                        v.side
                                        : "") +
                                    '" data-name="' + v.description + '"/>';
                            } else if (v.svg_type == "line") {
                                svg_el =
                                    "<line " +
                                    (v.stroke == 1 ? 'stroke="#333333"' : "") +
                                    ' stroke-width="2" x1="' +
                                    v.x1 +
                                    '" x2="' +
                                    v.x2 +
                                    '" y1="' +
                                    v.y1 +
                                    '" y2="' +
                                    v.y2 +
                                    '" id="line_' +
                                    v.id +
                                    '"/>';
                            } else if (v.svg_type == "polygon") {
                                svg_el =
                                    '<polygon fill="none" id="path_' +
                                    v.id +
                                    '" class="each-svg-el each-path-' +
                                    v.face.toUpperCase() +
                                    " path_" +
                                    v.description + ' '
                                    + fill_class +
                                    (v.description != '' ? ' clickable-path' : '') + '" points="' +
                                    v.svg_path +
                                    '" data-name="' + v.description + '"/>';
                            }
                            /*
                            if (fill_class == 'stain-not-editable') {
                                svg_el += " <svg width='20' height='20'> <g><circle cx='10' cy='10' r='10' stroke-width='4' fill='#EF8F00'/><text x='50%'' y='50%'' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em'>"+index+"</text></g></svg>";
                            } else if (fill_class == 'damage-not-editable') {
                                svg_el += " <svg width='20' height='20'> <g><circle cx='10' cy='10' r='10' stroke-width='4' fill='#EB5757'/><text x='50%'' y='50%'' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em'>"+index+"</text></g></svg>";

                            }
                            */

                            if (props.stainzone && props.stainzone.some(z => z.id_zone === v.id)) {
                                svg_el += "<circle cx='"+v.label_x+"' cy='"+v.label_y+"' r='10' stroke-width='4' fill='#EF8F00'  class='zone_labels' id='mycircle"+v.id+"'/><text x='"+v.label_x+"' y='"+v.label_y+"' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em' class='zone_labels' id='mytext"+v.id+"'>"+index+"</text>";
                            }else if (props.damagezone && props.damagezone.some(z => z.id_zone === v.id)) {
                                svg_el += "<circle cx='"+v.label_x+"' cy='"+v.label_y+"' r='10' stroke-width='4' fill='#EB5757'  class='zone_labels' id='mycircle"+v.id+"'/><text x='"+v.label_x+"' y='"+v.label_y+"' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em' id='mytext"+v.id+"'>"+index+"</text>";
                            }

                            details += svg_el;
                        });

                        picto_details.value = details;

                    }
                })
                .catch((error) => console.log(error))
                .finally(() => {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
        };

        watch(
            () => props.pictoname,
            (current_val, previous_val) => {
                 //Enleve les zones des ancien pictos
                document.querySelectorAll('.zone_labels').forEach(el=>el.remove());

                loadSvgZones(current_val);
            }
        );
        loadSvgZones(props.pictoname);

        function getPictoZoneDesc(id) {
            return svg_data.value.filter((zone) => zone.id === id)[0].description;
        }
        function onPartClick(e) {
            if (props.selectable) {
                if (e.target.matches('.clickable-path')) {
                    let svg_row = svg_data.value.filter(
                        (s) => s.id == e.target.id.split("_")[1]
                    );
                    svg_row = _.cloneDeep(svg_row);
                    if (svg_row[0].description != "") {


                        let svg_desc = svg_data.value.filter(
                            (s) => s.id == svg_row[0].id
                        );
                        context.emit("add-stain-zone", svg_row[0].id);

                        //if(route.name=='ComponentTest'){
                            //console.log(svg_row[0]);

                            var svgNS = "http://www.w3.org/2000/svg";

                            let label_id = "mycircle"+svg_row[0].id;
                            let label_txt_id = "mytext"+svg_row[0].id;

                            let color_fill = "#EF8F00";

                            if(props.damagezone){
                                color_fill = "#EB5757";
                            }

                            var myCircle = document.createElementNS(svgNS,"circle");
                            myCircle.setAttributeNS(null,"id",label_id);
                            myCircle.setAttributeNS(null,"class","zone_labels");
                            myCircle.setAttributeNS(null,"cx",parseInt(svg_row[0].label_x));
                            myCircle.setAttributeNS(null,"cy",parseInt(svg_row[0].label_y));
                            myCircle.setAttributeNS(null,"r",10);
                            myCircle.setAttributeNS(null,"fill",color_fill);
                            myCircle.setAttributeNS(null,"stroke",4);


                            var myText = document.createElementNS(svgNS,"text");
                            myText.setAttributeNS(null,"id",label_txt_id);
                            myText.setAttributeNS(null,"class","zone_labels");
                            myText.setAttributeNS(null,"x",parseInt(svg_row[0].label_x));
                            myText.setAttributeNS(null,"y",parseInt(svg_row[0].label_y));
                            myText.setAttributeNS(null,"text-anchor","middle");
                            myText.setAttributeNS(null,"stroke","white");
                            myText.setAttributeNS(null,"stroke-width","1px");
                            myText.setAttributeNS(null,"dy",".3em");

                            var index = 0;

                            if(route.name=='ComponentTest'){
                                index =  props.stainzone.filter(z => z.id_zone === svg_row[0].id).length>0?
                             props.stainzone.filter(z => z.id_zone === svg_row[0].id)[0].index:0;
                            }

                            if(route.name=='DetailingItem'){
                                if(props.stainzone){
                                    props.stainzone.forEach(function(v,i){
                                        if(v.id_zone==svg_row[0].id){
                                            index = i+1;
                                        }
                                    });
                                }

                                if(props.damagezone){
                                    props.damagezone.forEach(function(v,i){
                                        if(v.id_zone==svg_row[0].id){
                                            index = i+1;
                                        }
                                    });
                                }
                            }

                            //console.log(index);

                            myText.textContent = index; //OR index

                            let is_active = 0;
                            if (!e.target.matches('.stain-editable') && !e.target.matches('.damage-editable')) {
                                //add cirle
                                 document.getElementById("Layer_1").appendChild(myCircle);
                                 document.getElementById("Layer_1").appendChild(myText);
                                is_active = 1;
                            }else{
                                //remove circle
                                let el_circle = document.getElementById(label_id);

                                if(el_circle){
                                    el_circle.remove();
                                }

                                let el_text = document.getElementById(label_txt_id);
                                if(el_text){
                                    el_text.remove();
                                }

                                is_active = 0
                            }
                            context.emit('get-zone-detail',svg_row[0],is_active);
                        //}



                        svg_desc = _.cloneDeep(svg_desc);
                        svg_desc.forEach(element => {
                            let el = document.getElementById("path_" + element.id);
                            if (el) {
                                if (props.issue_type == "stain") {
                                    if (!e.target.matches('.stain-editable')) {
                                        el.classList.add('stain-editable');
                                    }
                                    else { el.classList.remove('stain-editable'); }
                                } else if (props.issue_type == "damage") {
                                    if (!e.target.matches('.damage-editable')) {
                                        el.classList.add('damage-editable');
                                    }
                                    else { el.classList.remove('damage-editable'); }
                                }
                            }
                        });
                    }
                }
            }else{
                context.emit('back-step');
            }
        }

        function getZone() {
            let old_fill = 'transparent';
            let selector = '.clickable-path';
            let labels = '.zone_labels';
            // We bind the event handler directly to the document.
            document.addEventListener('mouseover', function (e) {
                let el = e.target;    // Check if it matches our previously defined selector
                //old_fill  =el.style.fill;

                if(!el.matches(labels)){
                    if (!el.matches(selector)) {
                        el.style.fill = 'transparent';
                        return;
                    }    // The method logic


                    el.classList.add('path-mouse-over');

                    /*
                    //select other zones with same name
                    let zone_name = el.getAttribute('data-name');
                    let other_el = document.querySelectorAll('[data-name="'+zone_name+'"]');

                    other_el.forEach(function(v,i){
                        v.classList.add('path-mouse-over');
                    });
                    */
                }

            });

            document.addEventListener('mouseout', function (e) {
                let el = e.target;    // Check if it matches our previously defined selector
                //console.log('el-out',el);

                if (!el.matches(selector)) {
                    return;
                }    // The method logic
                el.classList.remove('path-mouse-over');

                //el.style.fill = '';//old_fill;
            });

        }

        getZone();

        function updateLabelPos(zone_id,typepos,val){
            let el_circle = document.getElementById("mycircle"+zone_id);
            let el_txt = document.getElementById("mytext"+zone_id);

            el_circle.setAttributeNS(null,"c"+typepos,val);
            el_txt.setAttributeNS(null,typepos,val);

        }

        return {
            loadSvgZones,
            onPartClick,
            svg_viewpoint,
            svg_scale,
            picto_details,
            svg_viewbox,
            getZone,
            updateLabelPos,
        };
    },
};
</script>
<style>
.svg_hover {
    background: silver;
}

.path-mouse-over {
    fill: #dadada !important;
}
.stain-editable {
    fill: #ef8f00 !important;
}
.stain-not-editable {
    fill: #f1d2a4b2 !important;
}
.damage-editable {
    fill: #eb5757 !important;
}
.damage-not-editable {
    fill: #f5ababb2 !important;
}
</style>
