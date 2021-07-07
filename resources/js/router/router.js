import {
    createRouter,
    createWebHistory,
} from  'vue-router';
import NotFound from '../components/NotFound';
import AuthenticationMiddleware from './middleware/authentication';

const router = createRouter({
    history:createWebHistory(),
    routes:[{
        path:'',
        name:'LandingPage',
        component:()=> import('../components/orderlist/OrderList'),
        meta:{
            authenticated:true
        }
        },
        {
            path:'/login/',
            name:'Login',
            component:()=> import('../components/auth/Login'),
            meta:{
                authenticated:false
            }
        },
        {
            path:'/loader/',
            name:'Loader',
            component:()=> import('../components/WaveLoader'),
            meta:{
                authenticated:false
            }
        },
        {
            path: '/:pathMatch(.*)',
            name: 'not-found',
            component:NotFound
        },

    ]
});

router.beforeEach(AuthenticationMiddleware);

export default router;
