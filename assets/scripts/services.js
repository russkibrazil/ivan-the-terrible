export default function getCrianca() {
    const cookies = decodeURIComponent(document.cookie).split(';');
    const cra = cookies.find(el => el.startsWith('cra=')).split('=')[1].split(',');
    return cra[0];
}