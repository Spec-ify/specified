import { writable } from 'svelte/store';

const profileData = writable();

function getData(){
    let jsonData: any
    
    profileData.subscribe((data)=>{
        jsonData = data
    })

    return jsonData
}

export const jsonData = getData()