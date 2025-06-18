<div id="modalDelete" onclick="closeModalDelete()">
    <form id="deleteForm">
        <input type="hidden" name="uuid" id="inpID" class="form-control">
        <h1>{{ isset($modalTitle) ? $modalTitle : 'Konfirmasi Hapus ' . ucwords($modalDelete) }}</h1>
        <i class="fa-solid fa-xmark" onclick="closeModalDelete()"></i>
        <p>{{ isset($modalMessage) ? $modalMessage : 'Apakah Anda yakin ingin menghapus ' . $modalDelete . ' ini?' }}</p>
        <div>
            <button type="button" class="btn-cancel" onclick="closeModalDelete()">{{ isset($cancelText) ? $cancelText : 'Batal' }}</button>
            <button type="submit" class="btn-delete" style="width: fit-content; height: fit-content;">{{ isset($confirmText) ? $confirmText : 'Hapus' }}</button>
        </div>
    </form>
</div>

<style>
#modalDelete {
    position: fixed;
    display: none;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
}

#modalDelete form {
    background-color: white;
    position: absolute;
    width: 400px;
    max-width: 90%;
    top: -20%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    padding: 20px;
    transition: all 0.3s ease;
}

#modalDelete form input {
    display: none;
}

#modalDelete form h1 {
    margin: 0 0 15px 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    padding-right: 20px;
}

#modalDelete form i {
    position: absolute;
    right: 20px;
    top: 20px;
    color: #777;
    font-size: 18px;
    cursor: pointer;
    transition: color 0.2s ease;
}

#modalDelete form i:hover {
    color: #333;
}

#modalDelete form p {
    margin: 0 0 20px 0;
    color: #555;
    font-size: 16px;
    line-height: 1.5;
}

#modalDelete form div {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

#modalDelete form div button {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

#modalDelete form .btn-cancel {
    background-color: #f1f1f1;
    color: #333;
}

#modalDelete form .btn-cancel:hover {
    background-color: #e0e0e0;
}

#modalDelete form .btn-delete {
    background-color: #dc3545;
    color: white;
}

#modalDelete form .btn-delete:hover {
    background-color: #c82333;
}

@keyframes showModalDelete {
    to {
        top: 50%;
        transform: translate(-50%, -50%);
    }
}

@keyframes closeModalDelete {
    to {
        top: -50%;
        transform: translate(-50%, -50%);
    }
}

@media screen and (max-width: 768px) {
    #modalDelete form {
        width: 350px;
        padding: 15px;
    }
    
    #modalDelete form h1 {
        font-size: 18px;
    }
    
    #modalDelete form p {
        font-size: 14px;
    }
    
    #modalDelete form div button {
        padding: 8px 16px;
        font-size: 14px;
    }
}

@media screen and (max-width: 480px) {
    #modalDelete form {
        width: 300px;
    }
    
    #modalDelete form h1 {
        font-size: 16px;
    }
    
    #modalDelete form p {
        font-size: 13px;
    }
    
    #modalDelete form div button {
        padding: 7px 14px;
        font-size: 13px;
    }
}
</style>