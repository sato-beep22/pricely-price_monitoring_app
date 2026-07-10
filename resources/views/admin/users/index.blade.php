<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="card bg-base-100 shadow-sm border border-base-300 animate-fade-in-up">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Name</th>
                            <th>Email & Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="hover">
                                <td>
                                    <div class="font-bold">{{ $user->name }}</div>
                                    @if($user->isBuyer() && $user->shop)
                                        <div class="text-xs opacity-70">{{ $user->shop->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $user->email }}</div>
                                    <div class="text-xs opacity-70">{{ $user->phone ?? 'No phone' }}</div>
                                </td>
                                <td>
                                    <div class="badge {{ $user->isAdmin() ? 'badge-primary' : ($user->isBuyer() ? 'badge-secondary' : 'badge-ghost') }} badge-sm">
                                        {{ ucfirst($user->role) }}
                                    </div>
                                </td>
                                <td>
                                    @if($user->isBuyer() && $user->shop)
                                        <div class="badge badge-success badge-sm badge-outline">Shop Active</div>
                                    @else
                                        <span class="text-base-content/40 text-sm">-</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Role Update Form -->
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="select select-bordered select-xs w-24">
                                            <option value="farmer" {{ $user->role === 'farmer' ? 'selected' : '' }}>Farmer</option>
                                            <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-xs btn-primary btn-outline">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-base-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
