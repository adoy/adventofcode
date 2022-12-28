#include <iostream>

using namespace std;

int main() {
	char v1, v2;
	int score1 = 0, score2 = 0;

	while (std::cin >> v1 >> v2) {
		v1 -= ('A' - 1);
		v2 -= ('X' - 1);

		switch ((v2 - v1 + 3) % 3) {
			case 0: score1 += 3 + v2; break;
			case 1: score1 += 6 + v2; break;
			case 2: score1 += v2;
		}

		switch (v2) {
			case 1: score2 += 0 + (v1 + 1) % 3 + 1; break;
			case 2: score2 += 3 + v1; break;
			case 3: score2 += 6 + (v1) % 3 + 1; break;
		}
	}

	cout << "Score 1: " << score1 << endl;
	cout << "Score 2: " << score2 << endl;
}
