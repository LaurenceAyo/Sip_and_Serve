import React, { useState } from 'react';
import { ShoppingBag, Plus, Edit3, LogOut } from 'lucide-react';

const SipServeDashboard = () => {
  const [activeTab, setActiveTab] = useState('inventory');
  const [selectedFilter, setSelectedFilter] = useState('ALL ITEMS');

  const inventoryItems = [
    { name: 'Almond Milk', inStock: 20, out: 3.0, current: '17.0 liters', status: 'good' },
    { name: 'Arabica Coffee Beans', inStock: 20, out: 5.5, current: '14.5 kg', status: 'good' },
    { name: 'Caramel Syrup', inStock: 20, out: 3.2, current: '16.8 liters', status: 'low' },
    { name: 'Espresso Blend', inStock: 20, out: 3.2, current: '16.8 kg', status: 'good' },
    { name: 'Kape Barako Beans', inStock: 20, out: 3.2, current: '16.8 kg', status: 'critical' },
    { name: 'Whole Milk', inStock: 20, out: 3.2, current: '16.8 liters', status: 'critical' },
    { name: 'White Sugar', inStock: 20, out: 15.6, current: '4.4 kg', status: 'good' },
    { name: 'Milk', inStock: 20, out: 3.2, current: '8.2 kg', status: 'good' },
    { name: 'Eggs', inStock: 20, out: 3.2, current: '16.8 kg', status: 'critical' },
    { name: 'Bread', inStock: 20, out: 3.2, current: '16.8 liters', status: 'critical' },
    { name: 'Kape Barako Beans', inStock: 20, out: 15.6, current: '4.4 kg', status: 'good' },
  ];

  const getStatusColor = (status) => {
    switch(status) {
      case 'good': return 'bg-green-500';
      case 'low': return 'bg-yellow-400';
      case 'critical': return 'bg-red-500';
      default: return 'bg-gray-300';
    }
  };

  const getStatusDot = (status) => {
    switch(status) {
      case 'good': return '🟢';
      case 'low': return '🟡';
      case 'critical': return '🔴';
      default: return '⚪';
    }
  };

  const renderInventoryContent = () => (
    <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
      <div className="flex justify-between items-center mb-6">
        <div className="flex items-center space-x-4">
          <div className="text-sm text-gray-600">Manager ID: <span className="font-medium">10023</span></div>
        </div>
        <button className="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
          <ShoppingBag size={16} />
          <span>Generate Shopping List</span>
        </button>
      </div>

      <div className="flex justify-between items-center mb-6">
        <div className="relative">
          <select 
            value={selectedFilter}
            onChange={(e) => setSelectedFilter(e.target.value)}
            className="bg-amber-100 border border-amber-200 rounded-lg px-4 py-2 pr-8 appearance-none cursor-pointer"
          >
            <option>ALL ITEMS</option>
            <option>COFFEE ITEMS</option>
            <option>DAIRY ITEMS</option>
            <option>SUPPLIES</option>
          </select>
          <div className="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <svg className="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>

        <div className="flex space-x-2">
          <button className="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <Plus size={16} />
            <span>ADD ITEM</span>
          </button>
          <button className="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg transition-colors">
            EDIT ITEMS
          </button>
        </div>
      </div>

      <div className="mb-4">
        <div className="text-sm font-medium mb-2">CURRENT STOCK LEVEL</div>
        <div className="flex items-center space-x-6">
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-green-500 rounded"></div>
            <span className="text-sm">Good</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-yellow-400 rounded"></div>
            <span className="text-sm">Low</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-4 h-4 bg-red-500 rounded"></div>
            <span className="text-sm">Critical</span>
          </div>
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full border-collapse">
          <thead>
            <tr className="bg-amber-100">
              <th className="border border-amber-200 p-3 text-left font-medium">ITEMS</th>
              <th className="border border-amber-200 p-3 text-center font-medium">IN</th>
              <th className="border border-amber-200 p-3 text-center font-medium">OUT</th>
              <th className="border border-amber-200 p-3 text-center font-medium">CURRENTLY IN STOCK</th>
              <th className="border border-amber-200 p-3 text-center font-medium">STATUS</th>
            </tr>
          </thead>
          <tbody>
            {inventoryItems.map((item, index) => (
              <tr key={index} className="hover:bg-amber-50 transition-colors">
                <td className="border border-amber-200 p-3 font-medium">{item.name}</td>
                <td className="border border-amber-200 p-3 text-center">{item.inStock}</td>
                <td className="border border-amber-200 p-3 text-center">{item.out}</td>
                <td className="border border-amber-200 p-3 text-center">{item.current}</td>
                <td className="border border-amber-200 p-3 text-center">
                  <div className={`w-8 h-8 rounded mx-auto ${getStatusColor(item.status)}`}></div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );

  const renderSalesContent = () => (
    <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
      <h2 className="text-2xl font-bold mb-6 text-gray-800">Sales Dashboard</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-green-50 p-6 rounded-lg">
          <h3 className="text-lg font-semibold text-green-800 mb-2">Today's Sales</h3>
          <p className="text-3xl font-bold text-green-600">₱8,450</p>
          <p className="text-sm text-green-600 mt-1">+12% from yesterday</p>
        </div>
        <div className="bg-blue-50 p-6 rounded-lg">
          <h3 className="text-lg font-semibold text-blue-800 mb-2">Orders Today</h3>
          <p className="text-3xl font-bold text-blue-600">47</p>
          <p className="text-sm text-blue-600 mt-1">+5 from yesterday</p>
        </div>
        <div className="bg-purple-50 p-6 rounded-lg">
          <h3 className="text-lg font-semibold text-purple-800 mb-2">Average Order</h3>
          <p className="text-3xl font-bold text-purple-600">₱180</p>
          <p className="text-sm text-purple-600 mt-1">+₱15 from yesterday</p>
        </div>
      </div>
    </div>
  );

  const renderProductsContent = () => (
    <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
      <h2 className="text-2xl font-bold mb-6 text-gray-800">Products Management</h2>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div className="border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow">
          <h3 className="font-semibold text-amber-800 mb-2">Espresso</h3>
          <p className="text-2xl font-bold text-amber-600">₱120</p>
          <p className="text-sm text-gray-600 mt-1">Hot coffee drink</p>
        </div>
        <div className="border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow">
          <h3 className="font-semibold text-amber-800 mb-2">Cappuccino</h3>
          <p className="text-2xl font-bold text-amber-600">₱150</p>
          <p className="text-sm text-gray-600 mt-1">Coffee with steamed milk</p>
        </div>
        <div className="border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow">
          <h3 className="font-semibold text-amber-800 mb-2">Caramel Latte</h3>
          <p className="text-2xl font-bold text-amber-600">₱180</p>
          <p className="text-sm text-gray-600 mt-1">Sweet coffee drink</p>
        </div>
      </div>
    </div>
  );

  const renderContent = () => {
    switch(activeTab) {
      case 'inventory': return renderInventoryContent();
      case 'sales': return renderSalesContent();
      case 'products': return renderProductsContent();
      default: return renderInventoryContent();
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-amber-100 to-orange-100 border-b border-amber-200">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-8">
              <h1 className="text-3xl font-bold text-amber-900">Sip & Serve</h1>
              <h2 className="text-4xl font-light text-amber-800 tracking-wide">CAFE DASHBOARD</h2>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 py-8">
        {renderContent()}
      </div>

      {/* Bottom Navigation */}
      <div className="fixed bottom-0 left-0 right-0 bg-amber-900 shadow-lg">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="flex justify-center space-x-8">
            <button
              onClick={() => setActiveTab('inventory')}
              className={`px-8 py-3 rounded-lg font-medium transition-colors ${
                activeTab === 'inventory' 
                  ? 'bg-amber-100 text-amber-900 border-2 border-amber-200' 
                  : 'bg-amber-800 text-white hover:bg-amber-700'
              }`}
            >
              INVENTORY
            </button>
            <button
              onClick={() => setActiveTab('sales')}
              className={`px-8 py-3 rounded-lg font-medium transition-colors ${
                activeTab === 'sales' 
                  ? 'bg-amber-100 text-amber-900 border-2 border-amber-200' 
                  : 'bg-amber-800 text-white hover:bg-amber-700'
              }`}
            >
              SALES
            </button>
            <button
              onClick={() => setActiveTab('products')}
              className={`px-8 py-3 rounded-lg font-medium transition-colors ${
                activeTab === 'products' 
                  ? 'bg-amber-100 text-amber-900 border-2 border-amber-200' 
                  : 'bg-amber-800 text-white hover:bg-amber-700'
              }`}
            >
              PRODUCT
            </button>
          </div>
          <div className="flex justify-start mt-4">
            <button className="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
              <LogOut size={16} />
              <span>LOG OUT</span>
            </button>
          </div>
        </div>
      </div>

      {/* Add padding to bottom to account for fixed navigation */}
      <div className="pb-32"></div>
    </div>
  );
};

export default SipServeDashboard;