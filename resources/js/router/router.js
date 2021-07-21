import {
    createRouter,
    createWebHistory,
} from  'vue-router';
import NotFound from '../components/NotFound';
import AuthenticationMiddleware from './middleware/authentication';

const router = createRouter({
    history:createWebHistory(),
    routes:[
        {
            path:'',
            name:'LandingPage',
            component:()=> import('../components/orderlist/OrderList'),
            meta:{
                authenticated:true
            },
            children:[
                {
                    path:'/order_details/:order_id',
                    name:'OrderDetails',
                    component:()=> import('../components/orderlist/OrderDetail'),
                    meta:{
                        authenticated:false
                    }
                }
                ]
        },
        {
            path:'/auth/',
            name:'AuthPage',
            component:()=> import('../components/auth/AuthPage'),
            meta:{
                authenticated:false
            },
            children:[
                {
                    path:'/auth/login/',
                    name:'Login',
                    component:()=> import('../components/auth/Login'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/forgot-password/',
                    name:'ForgotPassword',
                    component:()=> import('../components/auth/ForgotPassword'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/new-password/:recovery_token',
                    name:'NewPassword',
                    component:()=> import('../components/auth/NewPassword'),
                    meta:{
                        authenticated:false
                    }
                }
            ]
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
