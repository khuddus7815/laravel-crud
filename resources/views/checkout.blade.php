<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8" x-data="checkout()" x-init="init()" x-cloak>
    <div class="flex justify-between items-center mb-8 pb-4 border-b">
        <h1 class="text-4xl font-bold text-gray-800">Checkout</h1>
        <a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Shop</a>
    </div>

    <div x-show="isCartEmpty" class="text-center bg-white p-12 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700">Your Cart is Empty</h2>
        <p class="text-gray-500 mt-2">You can't proceed to checkout without any items.</p>
        <a href="{{ route('shop.index') }}" class="mt-6 inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700">
            Go Shopping
        </a>
    </div>

    <div x-show="!isCartEmpty" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1 order-last md:order-first">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
                <div class="space-y-4">
                    <template x-for="item in Object.values(cart)" :key="item.id">
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-semibold" x-text="`${item.name} (x${item.quantity})`"></p>
                                <p class="text-sm text-gray-500" x-text="`₹${(item.price * item.quantity).toFixed(2)}`"></p>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-6 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span x-text="`₹${subtotal}`"></span>
                    </div>
                    <div x-show="discount > 0" class="flex justify-between text-green-600">
                        <span>Discount (<span x-text="coupon.code"></span>)</span>
                        <span x-text="`- ₹${discount.toFixed(2)}`"></span>
                    </div>
                    <div class="flex justify-between font-bold text-xl pt-2 border-t">
                        <span>Total</span>
                        <span x-text="`₹${total}`"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow-md">
                @auth('customer')
                {{-- This whole section will only be visible to logged-in customers --}}
                <h2 class="text-2xl font-bold mb-4">Shipping Details</h2>
                <div class="space-y-2 bg-gray-50 p-4 rounded-md border mb-8">
                    <p><span class="font-medium text-gray-600">Name:</span> <span x-text="customer.name"></span></p>
                    <p><span class="font-medium text-gray-600">Email:</span> <span x-text="customer.email"></span></p>
                    <p class="text-sm text-gray-500 mt-2">Placing order as a registered customer.</p>
                </div>

                <h2 class="text-2xl font-bold mt-8 mb-4">Coupon Code</h2>
                <div class="flex space-x-2">
                    <input type="text" x-model="coupon.input" placeholder="Enter coupon code" class="flex-grow border-gray-300 rounded-md shadow-sm">
                    <button @click="applyCoupon()" :disabled="coupon.loading" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 disabled:bg-gray-400">
                        <span x-show="!coupon.loading">Apply</span>
                        <span x-show="coupon.loading">...</span>
                    </button>
                </div>
                <p x-show="coupon.message" :class="coupon.applied ? 'text-green-600' : 'text-red-600'" class="text-sm mt-2" x-text="coupon.message"></p>

                <h2 class="text-2xl font-bold mt-8 mb-4">Payment Method</h2>
                <div class="space-y-4">
                    {{-- Payment method options... --}}
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer" :class="{ 'border-indigo-600 ring-2 ring-indigo-200': paymentMethod === 'cod' }">
                        <input type="radio" name="payment" x-model="paymentMethod" value="cod" class="form-radio h-5 w-5 text-indigo-600">
                        <span class="ml-4 font-medium">Cash on Delivery</span>
                    </label>
                </div>

                <button @click="placeOrder()" :disabled="isProcessing" class="w-full mt-8 bg-indigo-600 text-white py-3 rounded-lg text-lg font-bold hover:bg-indigo-700 disabled:bg-gray-400">
                    <span x-show="!isProcessing" x-text="`Place Order (Pay ₹${total})`"></span>
                    <span x-show="isProcessing">Processing...</span>
                </button>

                @else
                {{-- Show this block if the user is a guest --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Please Log In</h2>
                    <p class="text-gray-600 mb-6">You need to be logged in to place an order.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('customer.login') }}" class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700">
                            Login
                        </a>
                        <a href="{{ route('customer.register') }}" class="inline-block bg-gray-200 text-gray-800 py-2 px-6 rounded-lg hover:bg-gray-300">
                            Register
                        </a>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
    
<script>
    function checkout() {
        return {
            cart: {},
            // Customer details are now initialized as empty strings
            customer: {
                name: '',
                email: ''
            },
            paymentMethod: 'cod', // Default to Cash on Delivery
            coupon: {
                input: '', code: '', message: '',
                loading: false, applied: false, details: null
            },
            isProcessing: false,
            orderSuccess: false,
            successfulOrderNumber: null, // To store the successful order number

            init() {
                const savedCart = localStorage.getItem('cart');
                if (savedCart) { this.cart = JSON.parse(savedCart); }
            },

            get isCartEmpty() { return Object.keys(this.cart).length === 0; },
            get subtotal() { return Object.values(this.cart).reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2); },
            get discount() {
                if (!this.coupon.applied || !this.coupon.details) return 0;
                if (this.coupon.details.type === 'percent') {
                    return (this.subtotal * this.coupon.details.value) / 100;
                }
                if (this.coupon.details.type === 'fixed') {
                    return parseFloat(this.coupon.details.value);
                }
                return 0;
            },
            get total() {
                const totalAmount = this.subtotal - this.discount;
                return totalAmount > 0 ? totalAmount.toFixed(2) : '0.00';
            },

            applyCoupon() {
                this.coupon.loading = true;
                this.coupon.message = '';
                fetch('/api/apply-coupon', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ code: this.coupon.input })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.valid) {
                        this.coupon.applied = true;
                        this.coupon.details = data.discount;
                        this.coupon.code = data.code;
                    }
                    this.coupon.message = data.message;
                })
                .catch(() => this.coupon.message = 'Something went wrong.')
                .finally(() => this.coupon.loading = false);
            },

            placeOrder() {
                this.isProcessing = true;

                fetch('/api/orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        // Send the customer name and email from the input fields
                        customer: this.customer,
                        cart: Object.values(this.cart),
                        coupon_code: this.coupon.applied ? this.coupon.code : null,
                        payment_method: this.paymentMethod,
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        // Handle server-side validation errors
                        return res.json().then(err => Promise.reject(err));
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.order_number) {
                        localStorage.removeItem('cart');
                        alert('Order placed successfully!');
                        window.location.href = '/orders';
                    } else {
                        alert(data.message || 'Failed to place order.');
                    }
                })
                .catch(err => alert(err.message || 'There was an error placing your order.'))
                .finally(() => this.isProcessing = false);
            }
        }
    }
</script>
</body>
</html>