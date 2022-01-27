<template>
    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 :width="this.svg_scale+'%'"  :viewBox="svg_viewpoint" :enable-background="'new '+svg_viewpoint" xml:space="preserve" style="pointer-events: fill;">
        <g id="svg_path" v-html="picto_details"></g>
    </svg>
</template>
<script>
import {ref,watch} from 'vue';
import {useStore} from 'vuex';
import {LOADER_MODULE,SET_LOADER_MSG,DISPLAY_LOADER,HIDE_LOADER} from '../../store/types/types';

export default {
    name:"ItemPicto",
    props:['pictoname'],
    setup(props) {
        const svg_viewpoint = ref('');
        const svg_scale = ref('');
        const picto_details = ref('');

        const store=useStore();

        const loadSvgZones = ((type_picto)=>{


                let zones = [{position:'sleave',face:'front',side:'left'},{position:'bottom',face:'back',side:''},{position:'collar',face:'front',side:''}];
                let details = '';

                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,'Please wait....']);

                axios.post('/item-picto',
                    {
                        item_type:type_picto,
                        zones:JSON.stringify(zones),
                        all_zones:1,
                        face:'front', //all
                    })
                    .then((res) => {
                       	//console.log(res.data);


                        if(res.data.svg_details){
                       	    svg_viewpoint.value = res.data.svg_details.viewpoint;
                       	    svg_scale.value = res.data.svg_details.scale;
						}


                        if(res.data.svg.length > 0){
                            let svg  = res.data.svg;

                            svg.forEach(v => {
                                let svg_el = '';

                                 if(v.svg_type=='path'){
                                    svg_el = '<path fill="none" id="path_'+v.id+'" class="each-svg-el each-path-'+v.face.toUpperCase()+' path_'+v.description+'" '+(v.stroke==1?'stroke="#333333"':'')+' stroke-width="2" d="'+v.svg_path+'" '+(v.clickable==1?'class="is_zone" style="cursor:pointer;"':'')+' data-pos="'+(v.clickable==1? v.position+' - '+v.face+' - '+v.side:'')+'"/>';
								}else if(v.svg_type=='line'){
                                    svg_el = '<line '+(v.stroke==1?'stroke="#333333"':'')+' stroke-width="2" x1="'+v.x1+'" x2="'+v.x2+'" y1="'+v.y1+'" y2="'+v.y2+'" id="line_'+v.id+'"/>';
								}else if(v.svg_type=='polygon'){
                                    svg_el = '<polygon fill="none" id="path_'+v.id+'" class="each-svg-el each-path-'+v.face.toUpperCase()+' path_'+v.description+'" points="'+v.svg_path+'" />';
								}


                                details += svg_el;
                            });

                            picto_details.value = details;

                        }
                    })
                    .catch(error => console.log(error))
                    .finally(()=>{
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
			});

 watch(() => props.pictoname, (current_val, previous_val) => {

                    loadSvgZones(current_val);

            });


   loadSvgZones(props.pictoname);

        return {
            loadSvgZones,
            svg_viewpoint,
            svg_scale,
            picto_details,
        }
    },
}
</script>
