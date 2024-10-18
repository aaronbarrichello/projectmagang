export const getPluck = (array,key) => array.map(a => a[key]);

export const getPIC = (visitors) => visitors.find(item => item.pic)?.name
