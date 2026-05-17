const products=[
    {id:1, name:"dounot",price:100,},
    {id:2, name:"cake",  price:200,},
    {id:3, name:"bread", price:50,},
];
let cart=[];
let orders =[];

function showPage(page){
    document.querySelectorAll('.container>div').forEach(div=>div.classList.add('hidden'));
    document.getElementById(page).classList.remove('hidden');
    if(page==="cart") rendercart();
    if(page==="orders") renderOrders();
}

function renderproducts() {
    const list=document.getElementById('product-list');
    list.innerHTML="";
    products.forEach(p=>{
        list.innerHTML+=`
        <div class="product">
        <img src="${p.img}" alt="${p.name}">
        <h3>${p.name}</h3>
        <p> $${p.price}</p>
        <button onclick="addToCart(${p.id})">Add to Cart</button>
        </div>
        `;  
     });
}
function addToCart(Id){
    const product = products.find(p=>p.id===Id);
    const item = cart.find(c=>c.id===Id);
    if (item){
        item.quantity++;
        }
        else{ cart.push({...product, quantity:1});}
        alert(`${product.name} added to cart`);
    }

function rendercart(){
        const tbody=document.getElementById('cart-items');
        tbody.innerHTML="";
        let total=0;
        cart.forEach(item=>{
            total+=item.price * item.quantity;
            tbody.innerHTML+=`
            <tr>
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>$${item.price}</td>
            <td>$${item.price * item.quantity}</td>
            <td><button onclick="removeFromCart(${item.id})">Remove</button></td>
            </tr>
            `;
        });
        document.getElementById('cart-total').innerText="Total: $"+total;
    }

    

function removeFromCart(Id){
    cart=cart.filter(item=>item.id!==Id);
    rendercart();
}
function placeOrder(e){
    e.preventDefault();
    if(cart.length===0){
        alert("Cart is empty");
        return;
    }
    const delivery=document.getElementById('delivery-option').value;
    const address=document.getElementById('address').value;
    const total=cart.reduce((sum,item)=>sum + item.price * item.quantity,0);
    const order ={
        id:orders.length + 1,
        items:[...cart],
        total:total,
        delivery:delivery,
        address:address,
        status:"Pending",
    };
    orders.push(order);
    cart=[];
    alert("Order placed successfully");
    showPage('orders');
}
function renderOrders(){
    const list =document.getElementById('order-history');
    list.innerHTML="";
    orders.forEach(order=>{
        list.innerHTML+=`
<li>Order #${order.id} | Total: $${order.total} | Status: ${order.status} | Delivery: ${order.delivery}</li>`;
});
}
function login(e){
    e.preventDefault();
    alert("Register Function (connect to backend)");

}
renderproducts();