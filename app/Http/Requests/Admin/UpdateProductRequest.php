<?php

namespace App\Http\Requests\Admin;

use App\Models\Subcategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'old_price' => ['required', 'numeric', 'min:0'],
            'new_price' => ['required', 'numeric', 'min:0', 'lte:old_price'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Ensure the selected subcategory belongs to the selected category.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $subcategoryId = $this->input('subcategory_id');
            $categoryId = $this->input('category_id');

            if ($subcategoryId && $categoryId) {
                $subcategory = Subcategory::find($subcategoryId);

                if (! $subcategory || (int) $subcategory->category_id !== (int) $categoryId) {
                    $validator->errors()->add(
                        'subcategory_id',
                        'The selected subcategory does not belong to the selected category.'
                    );
                }
            }

            // Prevent recycling a slug that exists in slug history (it belongs to another product's past),
            // unless the history record belongs to the product currently being updated.
            $slug = $this->input('slug');
            $productId = $this->route('product')?->id ?? $this->route('product');

            if ($slug && ! $validator->errors()->has('slug')) {
                $conflict = \App\Models\ProductSlugHistory::where('old_slug', $slug)
                    ->when($productId, fn ($q) => $q->where('product_id', '!=', $productId))
                    ->exists();
                if ($conflict) {
                    $validator->errors()->add('slug', 'This slug was previously used by another product and cannot be reused.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'new_price.lte' => 'The new price must be less than or equal to the old price.',
        ];
    }
}
