<template>
    <svg
        version="1.1"
        id="Layer_1"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        x="0px"
        y="0px"
        :viewBox="svg_viewpoint"
        :enable-background="'new ' + svg_viewpoint"
        xml:space="preserve"
        style="pointer-events: fill"
        @click="onPartClick"
    >
        <g id="svg_path" v-html="picto_details" />
    </svg>
</template>
<script>
import { ref, watch } from "vue";
import { useStore } from "vuex";
import {
    LOADER_MODULE,
    SET_LOADER_MSG,
    DISPLAY_LOADER,
    HIDE_LOADER,
} from "../../store/types/types";

export default {
    name: "ItemPicto",
    props: ["pictoname", "face", "selectable", "issue_type", "stainzone", "damagezone"],
    emits: ['add-stain-zone'],
    setup(props, context) {
        const svg_viewpoint = ref("");
        const svg_scale = ref("");
        const picto_details = ref("");
        const svg_data = ref({});

        const store = useStore();

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
                    face: props.face ? props.face : "front", //all or front
                })
                .then((res) => {
                    //console.log(res.data);

                    if (res.data.svg_details) {
                        svg_viewpoint.value = res.data.svg_details.viewpoint;
                        svg_scale.value = res.data.svg_details.scale;
                    }

                    if (res.data.svg.length > 0) {
                        let svg = res.data.svg;
                        svg_data.value = res.data.svg;
                        svg.forEach((v) => {
                            let svg_el = "";
                            if (v.svg_type == "path") {
                                let fill = "";
                                if (props.issue_type == "stain") {
                                    fill = v.description && props.stainzone && props.stainzone.some(z => getPictoZoneDesc(z.id_zone) === v.description) ? '#F1D2A4B2' : 'none';
                                } else if (props.issue_type == "damage"){
                                    console.log(props.damagezone)
                                    fill = v.description && props.damagezone && props.damagezone.some(z => getPictoZoneDesc(z.id_zone) === v.description) ? '#F5ABABB2' : 'none';
                                }else{
                                    fill='none';
                                }
                                svg_el =
                                    '<path fill="' + fill +
                                    '"id="path_' +
                                    v.id +
                                    '" class="each-svg-el each-path-' +
                                    (v.face?v.face.toUpperCase():'') +
                                    " path_" +
                                    v.description +
                                    '" ' +
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
                                        (v.face?v.face:'') +
                                        " - " +
                                        v.side
                                        : "") +
                                    '" />';
                                if (fill == '#F1D2A4B2') {
                                   svg_el += " <svg width='20' height='20'> <g><circle cx='10' cy='10' r='10' stroke-width='4' fill='#EF8F00'/><text x='50%'' y='50%'' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em'>1</text></g></svg>";
                                }else if(fill == '#F5ABABB2') {
                                    svg_el += " <svg width='20' height='20'> <g><circle cx='10' cy='10' r='10' stroke-width='4' fill='#EB5757'/><text x='50%'' y='50%'' text-anchor='middle' stroke='white' stroke-width='1px' dy='.3em'>1</text></g></svg>";
                                }
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
                                    (v.face?v.face.toUpperCase():'') +
                                    " path_" +
                                    v.description +
                                    '" points="' +
                                    v.svg_path +
                                    '" />';
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
                loadSvgZones(current_val);
            }
        );
        loadSvgZones(props.pictoname);

        function getPictoZoneDesc(id){
            return svg_data.value.filter((zone)=> zone.id===id)[0].description;
        }
        function onPartClick(e) {
            if (props.selectable) {
                if (e.target.tagName === "path") {
                    let svg_row = svg_data.value.filter(
                        (s) => s.id == e.target.id.split("_")[1]
                    );
                    svg_row = _.cloneDeep(svg_row);
                    if (svg_row[0].description != "") {
                        let svg_desc = svg_data.value.filter(
                            (s) => s.description == svg_row[0].description && s.svg_type == "path"
                        );
                        context.emit("add-stain-zone", svg_row[0].id);
                        svg_desc = _.cloneDeep(svg_desc);
                        svg_desc.forEach(element => {
                            let el = document.getElementById("path_" + element.id);
                            if (el) {
                                if (el.style.fill == "" || el.style.fill == "transparent") {
                                    el.style.fill = props.issue_type == "stain" ? "#EF8F00" : "#EB5757";
                                } else {
                                    el.style.fill = "transparent";
                                }
                            }
                        });
                    }
                }
            }
        }

        return {
            loadSvgZones,
            onPartClick,
            svg_viewpoint,
            svg_scale,
            picto_details,
        };
    },
};
</script>
