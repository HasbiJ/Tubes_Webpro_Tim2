import java.util.Scanner;

public class array {
    public static int hitungPendapatan(int[] jumlahTerjual, int[] hargaRoti) {
        int total = 0;

        for(int j=0; j<jumlahTerjual.length; j++){
            total += (jumlahTerjual [j] * hargaRoti[j]);
        }
        return total;
    }
    public static int hitungTotalPendapatan(int[] pendapatanHarian) {
        int pendapatan = 0;
        for(int y=0; y<pendapatanHarian.length; y++){
            pendapatan += pendapatanHarian[y];
        }
        return pendapatan;
    }

    public static void main(String[] args) {
        Scanner input = new Scanner(System.in);
        System.out.print("Nama Toko: ");
        String namaToko = input.nextLine();
        System.out.print("Jumlah Hari Penjualam: ");
        int jumlahHari = input.nextInt();

        int[] pendapatanHarian = new int [jumlahHari];

        for (int i = 0; i < jumlahHari; i++) {
            System.out.println("Hari Ke-" + (i + 1) + ": ");
            System.out.print("Jumlah jenis roti terjual: ");
            int jumlahRoti = input.nextInt();
            int [] jumlahTerjual = new int[jumlahRoti];
            int [] hargaRoti = new int[jumlahRoti];

            for (int x = 0; x < jumlahRoti; x++) {
                System.out.println("Jenis Roti ke-" + (x + 1) + ": ");
                System.out.print("Nama roti: ");
                String namaRoti = input.nextLine();
                input.nextLine();
                System.out.print("Jumlah Terjual: ");
                jumlahTerjual[x] = input.nextInt();
                
                System.out.print("Harga per roti: ");
                hargaRoti[x] = input.nextInt();

            }
            int t = hitungPendapatan(jumlahTerjual, hargaRoti);
            System.out.println("Pendapatan Hari ke- "+ (i+1)+ " : " + t);

            pendapatanHarian[i] += t;
        }
        int totalPendapatan = hitungTotalPendapatan(pendapatanHarian);
        System.out.println("Totak Pendapatan" + namaToko + " Rp " + totalPendapatan);
    }
}
