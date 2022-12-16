#include <algorithm>
#include <iostream>
#include <iterator>
#include <string>
#include <vector>

using namespace std;

class Sensor {
public:
  Sensor(int x, int y, int beaconX, int beaconY) {
    this->x = x;
    this->y = y;
    this->beaconX = beaconX;
    this->beaconY = beaconY;
	this->distance = abs(beaconX - x) + abs(beaconY - y);
  }
  int x;
  int y;
  int beaconY;
  int beaconX;
  int distance;
};

class Map {
private:
  vector<Sensor> sensors;

public:
  Map(vector<Sensor> sensors) { this->sensors = sensors; }

  int countPositionsNotHavingBeaconsOnLine(int y) {
    int freePositions = 0;
    vector<pair<int, int>> ranges;

    for (Sensor sensor : sensors) {
      if (y >= sensor.y - sensor.distance && y <= sensor.y + sensor.distance) {
        int hd = abs(abs(sensor.y - y) - sensor.distance);
        if (sensor.beaconY == y && sensor.beaconX >= sensor.x - hd &&
            sensor.beaconX <= sensor.x + hd) {
          ranges.push_back(pair<int, int>(sensor.x - hd, sensor.beaconX - 1));
          ranges.push_back(pair<int, int>(sensor.beaconX + 1, sensor.x + hd));
        } else {
          ranges.push_back(pair<int, int>(sensor.x - hd, sensor.x + hd));
        }
      }
    }

    sort(ranges.begin(), ranges.end());

    if (ranges.empty()) {
      return 0;
    }

    pair<int, int> previous = ranges[0];
    ranges.erase(ranges.begin());
    for (pair<int, int> range : ranges) {
      if (range.first <= previous.second && range.second > previous.second) {
        previous = {previous.first, range.second};
      } else if (range.first > previous.second) {
        freePositions += previous.second - previous.first + 1;
        previous = range;
      }
    }

    freePositions += previous.second - previous.first + 1;

    return freePositions;
  }

  bool canHaveBeacon(int x, int y) {
    for (Sensor sensor : sensors) {
      if (sensor.distance >= abs(x - sensor.x) + abs(y - sensor.y)) {
        return false;
      }
    }
    return true;
  }

  long long getTuningFrequency(int lowerBound, int higherBound) {
    for (Sensor sensor : sensors) {
      int md = sensor.distance + 1;
      int minY = max(lowerBound, sensor.y - md);
      int maxY = min(higherBound, sensor.y + md);
      for (int y = minY; y <= maxY; y++) {
        int hd = abs(abs(sensor.y - y) - md);
        for (int x = sensor.x - hd; x <= sensor.x + hd; x += 2 * hd) {
          if (x >= lowerBound && x <= higherBound && canHaveBeacon(x, y)) {
            return (long long)x * 4000000 + y;
          } else if (0 == hd) {
            break;
          }
        }
      }
    }
    return -1;
  }
};

int main(int argc, char **argv) {
  string line;
  vector<Sensor> sensors;

  while (getline(cin, line)) {
    int x, y, beaconX, beaconY;
    sscanf(line.c_str(), "Sensor at x=%d, y=%d: closest beacon is at x=%d, y=%d", &x, &y, &beaconX, &beaconY);
    sensors.push_back(Sensor(x, y, beaconX, beaconY));
  }

  Map map(sensors);
  cout << "Result 1: " << map.countPositionsNotHavingBeaconsOnLine(argc > 1 ? stoi(argv[1]) : 2000000) << endl;
  cout << "Result 2: " << map.getTuningFrequency(0, argc > 2 ? stoi(argv[2]) : 4000000) << endl;
}
