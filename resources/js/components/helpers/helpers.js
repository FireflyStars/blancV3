

export const hasRoles=roles=>{
    const ses_roles=JSON.parse(window.atob(window.sessionStorage.getItem('roles')));

    let foundroles=Array();
    roles.forEach(role=> {
            let foundrole = ses_roles.filter(ses_role => ses_role.name == role);
            if(foundrole.length>0)
            foundroles.push(
                foundrole
            )
        }
    );

    return foundroles.length>0;
};

export const formatPrice=price=>`£${price !== 0 ? price.toFixed(2) : 0}`;