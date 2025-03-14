<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FreelancerController extends Controller
{
    public function index()
    {
        $data = Freelancer::with(['user', 'category'])->get();
        return success($data, 'Data freelancer berhasil diambil', Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id'     => 'required|numeric|exists:users,id',
                'category_id' => 'required|numeric',
                'description' => 'required|string',
                'price'       => 'required|numeric',
            ]);

            $data = Freelancer::create($request->all());

            return success($data, 'Data freelancer berhasil disimpan', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $data = Freelancer::with(['user', 'category'])->find($id);

        if (! $data) {
            return error('Data freelancer tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        return success($data, 'Data freelancer berhasil diambil', Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id'     => 'required|numeric|exists:users,id',
                'category_id' => 'required|numeric',
                'description' => 'required|string',
                'price'       => 'required|numeric',
            ]);

            $data = Freelancer::find($id);

            if (! $data) {
                return error('Data freelancer tidak ditemukan', Response::HTTP_NOT_FOUND);
            }

            $data->update($request->all());

            return success($data, 'Data freelancer berhasil diubah', Response::HTTP_OK);
        } catch (\Exception $e) {
            return error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $data = Freelancer::find($id);

        if (! $data) {
            return error('Data freelancer tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return success(null, 'Data freelancer berhasil dihapus', Response::HTTP_OK);
    }
}
