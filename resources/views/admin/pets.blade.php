@extends('layouts.app')

@section('title', 'Pet Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#2d3748]">
            <i class="fas fa-paw text-[#fcd34d] mr-2"></i>Pet Management
        </h1>
        <a href="{{ route('pets.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-[#213B63] text-white rounded-lg hover:bg-[#003d82] transition-colors shadow-md">
            <i class="fas fa-plus-circle mr-2 text-[#fcd34d]"></i>
            Add New Pet
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Pending Approval Section (Collapsible) -->
    @if($pendingPets->count() > 0)
    <div class="mb-6 bg-yellow-100 rounded-lg shadow-md border-l-4 border-yellow-500 overflow-hidden">
        <button onclick="toggleSection('pending')" 
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-yellow-100 transition-colors">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-600 mr-3 text-xl"></i>
                <h2 class="text-lg font-bold text-gray-800">
                    Pending Approval ({{ $pendingPets->count() }})
                </h2>
            </div>
            <i id="pending-icon" class="fas fa-chevron-up text-gray-600 transition-transform"></i>
        </button>
        
        <div id="pending-section" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-yellow-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Pet Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Species/Breed</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Owner</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Registered</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($pendingPets as $pet)
                    <tr class="border-b border-gray-200 hover:bg-yellow-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-paw text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $pet->name }}</p>
                                    <p class="text-xs text-gray-500">Age: {{ $pet->age }} years</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-800 font-medium">{{ ucfirst($pet->species) }}</p>
                            @if($pet->breed)
                                <p class="text-xs text-gray-500">{{ $pet->breed }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-800">{{ $pet->owner->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $pet->owner->user->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $pet->created_at->format('M d, Y') }}
                            <p class="text-xs text-gray-400">{{ $pet->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('pets.show', $pet->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition"
                                   title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                
                                <form action="{{ route('pets.approve', $pet->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-800 transition"
                                            onclick="return confirm('Approve this pet registration?')"
                                            title="Approve">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </button>
                                </form>
                                
                                <button onclick="showRejectModal({{ $pet->id }}, '{{ $pet->name }}')"
                                        class="text-red-600 hover:text-red-800 transition"
                                        title="Reject">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>

                                <a href="{{ route('pets.edit', $pet->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 transition"
                                   title="Edit">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.pets') }}" class="flex gap-2">
            <input type="text" name="search" 
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#213B63] focus:border-transparent" 
                   placeholder="Search approved pets by name, species, breed, or owner..." 
                   value="{{ request('search') }}">
            <button type="submit" 
                    class="px-6 py-2.5 bg-[#213B63] text-white rounded-lg hover:bg-[#003d82] transition-colors shadow-md">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.pets') }}" 
                   class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors shadow-md">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Approved Pets Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-[#1e3a5f] px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-2"></i>
                Approved Pets ({{ $approvedPets->total() }})
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#2d4a6f] text-white">
                    <tr>
                        <th class="px-6 py-3 text-sm font-semibold">#</th>
                        <th class="px-6 py-3 text-sm font-semibold">Pet Name</th>
                        <th class="px-6 py-3 text-sm font-semibold">Species/Breed</th>
                        <th class="px-6 py-3 text-sm font-semibold">Owner</th>
                        <th class="px-6 py-3 text-sm font-semibold">Registered</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedPets as $pet)
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-600">{{ $loop->iteration + ($approvedPets->currentPage() - 1) * $approvedPets->perPage() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center mr-3 shadow-sm">
                                        <i class="fas fa-paw text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $pet->name }}</p>
                                        <p class="text-xs text-gray-500">Age: {{ $pet->age }} years</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800 font-medium">{{ ucfirst($pet->species) }}</p>
                                @if($pet->breed)
                                    <p class="text-xs text-gray-500">{{ $pet->breed }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800">{{ $pet->owner->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $pet->owner->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pet->created_at->format('M d, Y') }}
                                <p class="text-xs text-gray-400">{{ $pet->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('pets.show', $pet->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition"
                                       title="View Details">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>

                                    <a href="{{ route('pets.edit', $pet->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 transition"
                                       title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-paw text-5xl mb-3 text-gray-300"></i>
                                <p class="text-gray-500 text-lg">No approved pets found{{ request('search') ? ' for "'.request('search').'"' : '' }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($approvedPets->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $approvedPets->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-bold flex items-center">
                <i class="fas fa-times-circle mr-2"></i>Reject Pet Registration
            </h3>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-4">
                Pet: <span id="rejectPetName" class="font-semibold text-gray-900"></span>
            </p>
            
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="rejection_reason" class="block text-sm font-semibold text-gray-700 mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                              placeholder="Please provide a detailed reason for rejecting this pet registration..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">This reason will be visible to the pet owner.</p>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-5 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-md">
                        <i class="fas fa-times-circle mr-1"></i> Reject Pet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSection(section) {
    const content = document.getElementById(section + '-section');
    const icon = document.getElementById(section + '-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function showRejectModal(petId, petName) {
    document.getElementById('rejectPetName').textContent = petName;
    document.getElementById('rejectForm').action = `/pets/${petId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>

@endsection