@extends('layouts.app')

@section('title', 'Товары - Админ-панель - EInvestor')

@section('content')
<div class="content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление товарами</h1>
        <a href="{{ route('admin.products.create') }}" class="btn">+ Создать товар</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию или SKU" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.products.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($products->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 1rem;">Название</th>
                <th style="text-align: left; padding: 1rem;">SKU</th>
                <th style="text-align: right; padding: 1rem;">Цена</th>
                <th style="text-align: center; padding: 1rem;">Статус</th>
                <th style="text-align: center; padding: 1rem;">Наличие</th>
                <th style="text-align: left; padding: 1rem;">Дата</th>
                <th style="text-align: center; padding: 1rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 1rem;">
                    <strong>{{ $product->name }}</strong>
                    @if($product->file_path)
                    <span style="font-size: 0.75rem; color: #16a34a; margin-left: 0.5rem;">📦</span>
                    @endif
                </td>
                <td style="padding: 1rem;">{{ $product->sku ?: '-' }}</td>
                <td style="padding: 1rem; text-align: right;">{{ number_format($product->price, 0, ',', ' ') }} ₽</td>
                <td style="padding: 1rem; text-align: center;">
                    <span style="padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; 
                        background: {{ $product->status === 'publish' ? '#d1fae5' : '#fee2e2' }};
                        color: {{ $product->status === 'publish' ? '#065f46' : '#991b1b' }};">
                        {{ $product->status === 'publish' ? 'Опубликовано' : 'Черновик' }}
                    </span>
                </td>
                <td style="padding: 1rem; text-align: center;">
                    {{ $product->stock_status === 'in_stock' ? 'В наличии' : 'Нет в наличии' }}
                </td>
                <td style="padding: 1rem;">{{ $product->created_at->format('d.m.Y') }}</td>
                <td style="padding: 1rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Редактировать</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: #dc2626;">Удалить</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Товаров не найдено</p>
    @endif
</div>
@endsection
